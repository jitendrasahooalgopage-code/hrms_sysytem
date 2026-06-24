<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [

        'asset_type',

        'serial_no',

        'cpu_serial_no',

        'monitor_serial_no',

        'imei',

        'sim_provider',

        'plan_days',

        'message',

        'status'
    ];
}