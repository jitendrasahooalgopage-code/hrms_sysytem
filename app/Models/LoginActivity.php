<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginActivity extends Model
{
    protected $fillable = [
        'user_id',
        'employee_code',
        'name',
        'email',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'platform',
        'session_id',
        'activity_type',
        'status',
        'login_method',
        'login_at',
        'logout_at',
        'session_duration',
        'location',
        'metadata',
        'lattitude',
        'longitude',
        'full_address'
    ];

    protected $casts = [
        'metadata' => 'array',
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}