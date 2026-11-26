<?php

namespace App\Http\Controllers;

use App\Models\DeviceState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    // ---------- WEB (dashboard) ----------
    public function index()
    {
        return view('kontrol');
    }

    public function status()
    {
        $s = DeviceState::first();
        return response()->json([
            'online'    => $s->last_seen && $s->last_seen->gt(now()->subSeconds(15)),
            'sensor_ok' => $s->sensor_ok,
            'temp'      => $s->temp,
            'setpoint'  => $s->setpoint,
            'mode_auto' => $s->mode_auto,
            'h1' => $s->a_h1, 'h2' => $s->a_h2, 'f1' => $s->a_f1, 'f2' => $s->a_f2,
        ]);
    }

    public function control(Request $r)
    {
        $d = $r->validate([
            'mode_auto' => 'sometimes|boolean',
            'setpoint'  => 'sometimes|numeric|min:15|max:55',
            'dev'       => 'sometimes|in:h1,h2,f1,f2',
            'state'     => 'sometimes|boolean',
        ]);

        $s = DeviceState::first();

        if (array_key_exists('mode_auto', $d)) {
            $s->mode_auto = $d['mode_auto'];
            $s->h1 = $s->h2 = $s->f1 = $s->f2 = false; // reset saat ganti mode
        }
        if (isset($d['setpoint'])) {
            $s->setpoint = $d['setpoint'];
        }
        if (isset($d['dev']) && !$s->mode_auto) {
            $s->{$d['dev']} = (bool) ($d['state'] ?? false);
        }
        $s->save();

        return response()->json(['ok' => true]);
    }

    // ---------- API (dipanggil ESP32) ----------
    private function checkKey(Request $r): void
    {
        abort_unless(hash_equals((string) config('services.device_key'), (string) $r->header('X-API-KEY')), 401);
    }

    public function command(Request $r)
    {
        $this->checkKey($r);
        $s = DeviceState::first();
        return response()->json($s->only(['mode_auto', 'setpoint', 'h1', 'h2', 'f1', 'f2']));
    }

    public function report(Request $r)
    {
        $this->checkKey($r);
        $d = $r->validate([
            'temp' => 'nullable|numeric', 'sensor_ok' => 'required|boolean',
            'h1' => 'required|boolean', 'h2' => 'required|boolean',
            'f1' => 'required|boolean', 'f2' => 'required|boolean',
        ]);

        $s = DeviceState::first();
        $s->update([
            'temp' => $d['temp'] ?? null, 'sensor_ok' => $d['sensor_ok'],
            'a_h1' => $d['h1'], 'a_h2' => $d['h2'], 'a_f1' => $d['f1'], 'a_f2' => $d['f2'],
            'last_seen' => now(),
        ]);

        // simpan riwayat suhu maksimal 1x per menit
        if ($d['sensor_ok'] && isset($d['temp'])) {
            $last = DB::table('temp_logs')->latest('created_at')->value('created_at');
            if (!$last || now()->diffInSeconds($last, true) >= 60) {
                DB::table('temp_logs')->insert(['temp' => $d['temp'], 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        return response()->json(['ok' => true]);
    }
}