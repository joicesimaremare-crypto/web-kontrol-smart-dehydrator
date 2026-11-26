<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceState extends Model
{
    protected $guarded = [];

    protected $casts = [
        'mode_auto' => 'boolean', 'sensor_ok' => 'boolean',
        'h1' => 'boolean', 'h2' => 'boolean', 'f1' => 'boolean', 'f2' => 'boolean',
        'a_h1' => 'boolean', 'a_h2' => 'boolean', 'a_f1' => 'boolean', 'a_f2' => 'boolean',
        'last_seen' => 'datetime',
    ];
}