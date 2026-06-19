<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'checkin_latitude',
        'checkin_longitude',
        'checkin_address',
        'checkin_accuracy',
        'checkout_latitude',
        'checkout_longitude',
        'checkout_address',
        'checkout_accuracy',
        'checkin_at',
        'checkout_at',
        'session_duration',
        'status',
        'ip_address',
        'device_type',
        'browser',
        'platform',
        'user_agent',
    ];

    protected $casts = [
        'checkin_at'  => 'datetime',
        'checkout_at' => 'datetime',
        'checkin_latitude'  => 'float',
        'checkin_longitude' => 'float',
        'checkout_latitude'  => 'float',
        'checkout_longitude' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: only active (checked in, not yet checked out) sessions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get the session duration as a human-readable string.
     */
    public function getFormattedDurationAttribute(): ?string
    {
        if (is_null($this->session_duration)) {
            return null;
        }

        $hours   = intdiv($this->session_duration, 3600);
        $minutes = intdiv($this->session_duration % 3600, 60);
        $seconds = $this->session_duration % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
