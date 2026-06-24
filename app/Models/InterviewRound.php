<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewRound extends Model
{
    protected $fillable = [
        'application_id', 'round_name', 'round_type', 'mode',
        'scheduled_at', 'duration_minutes', 'meeting_link',
        'location', 'interviewer_id', 'outcome',
        'rating', 'feedback', 'internal_notes', 'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public static function outcomes(): array
    {
        return [
            'pending'     => ['label' => 'Pending',     'color' => 'warning'],
            'passed'      => ['label' => 'Passed',      'color' => 'success'],
            'failed'      => ['label' => 'Failed',      'color' => 'danger'],
            'no_show'     => ['label' => 'No Show',     'color' => 'dark'],
            'rescheduled' => ['label' => 'Rescheduled', 'color' => 'info'],
        ];
    }

    public static function modes(): array
    {
        return [
            'online'    => 'Online / Video Call',
            'in_person' => 'In Person',
            'phone'     => 'Phone Call',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getOutcomeLabelAttribute(): string
    {
        return self::outcomes()[$this->outcome]['label'] ?? 'Pending';
    }

    public function getOutcomeColorAttribute(): string
    {
        return self::outcomes()[$this->outcome]['color'] ?? 'secondary';
    }

    public function getRatingStarsAttribute(): string
    {
        if (!$this->rating) return '—';
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }
}
