<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('device_states', function (Blueprint $t) {
            $t->id();
            $t->boolean('mode_auto')->default(true);
            $t->float('setpoint')->default(30);
            // perintah manual dari web
            $t->boolean('h1')->default(false);
            $t->boolean('h2')->default(false);
            $t->boolean('f1')->default(false);
            $t->boolean('f2')->default(false);
            // kondisi nyata yang dilaporkan ESP32
            $t->float('temp')->nullable();
            $t->boolean('sensor_ok')->default(false);
            $t->boolean('a_h1')->default(false);
            $t->boolean('a_h2')->default(false);
            $t->boolean('a_f1')->default(false);
            $t->boolean('a_f2')->default(false);
            $t->timestamp('last_seen')->nullable();
            $t->timestamps();
        });

        Schema::create('temp_logs', function (Blueprint $t) {
            $t->id();
            $t->float('temp');
            $t->timestamps();
        });

        DB::table('device_states')->insert(['created_at' => now(), 'updated_at' => now()]);
    }

    public function down(): void
    {
        Schema::dropIfExists('temp_logs');
        Schema::dropIfExists('device_states');
    }
};