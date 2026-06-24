<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationActivity extends Model
{
    protected $fillable = [
        'application_id', 'type', 'description', 'meta', 'created_by',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'stage_changed'        => 'fas fa-exchange-alt text-primary',
            'interview_scheduled'  => 'fas fa-calendar-check text-info',
            'interview_completed'  => 'fas fa-check-circle text-success',
            'note_added'           => 'fas fa-sticky-note text-warning',
            'cv_uploaded'          => 'fas fa-file-upload text-secondary',
            'offer_sent'           => 'fas fa-handshake text-success',
            'rejected'             => 'fas fa-times-circle text-danger',
            default                => 'fas fa-circle text-muted',
        };
    }
}
