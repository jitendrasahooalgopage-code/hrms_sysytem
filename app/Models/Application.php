<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    protected $fillable = [
        'candidate_id', 'job_position_id', 'stage', 'status',
        'stage_order', 'rejection_reason', 'source',
        'expected_salary', 'available_from', 'assigned_to', 'created_by',
    ];

    protected $casts = [
        'available_from' => 'date',
        'expected_salary' => 'decimal:2',
    ];

    // ─── Stage config ──────────────────────────────────────────────────────────

    public static function stages(): array
    {
        return [
            'applied'      => ['label' => 'Applied',       'color' => 'secondary', 'order' => 0, 'icon' => 'fas fa-file-alt'],
            'screening'    => ['label' => 'Screening',     'color' => 'info',      'order' => 1, 'icon' => 'fas fa-search'],
            'technical'    => ['label' => 'Technical',     'color' => 'primary',   'order' => 2, 'icon' => 'fas fa-code'],
            'hr_interview' => ['label' => 'HR Interview',  'color' => 'warning',   'order' => 3, 'icon' => 'fas fa-users'],
            'final_round'  => ['label' => 'Final Round',   'color' => 'orange',    'order' => 4, 'icon' => 'fas fa-trophy'],
            'offer'        => ['label' => 'Offer',         'color' => 'purple',    'order' => 5, 'icon' => 'fas fa-handshake'],
            'hired'        => ['label' => 'Hired',         'color' => 'success',   'order' => 6, 'icon' => 'fas fa-check-circle'],
            'rejected'     => ['label' => 'Rejected',      'color' => 'danger',    'order' => 7, 'icon' => 'fas fa-times-circle'],
        ];
    }

    public static function sources(): array
    {
        return [
            'linkedin'   => 'LinkedIn',
            'referral'   => 'Referral',
            'job_board'  => 'Job Board',
            'website'    => 'Company Website',
            'walk_in'    => 'Walk-in',
            'other'      => 'Other',
        ];
    }

    public function getStageConfigAttribute(): array
    {
        return self::stages()[$this->stage] ?? ['label' => $this->stage, 'color' => 'secondary', 'icon' => 'fas fa-circle'];
    }

    public function getStageLabelAttribute(): string
    {
        return $this->stageConfig['label'];
    }

    public function getStageBadgeColorAttribute(): string
    {
        return $this->stageConfig['color'];
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function jobPosition(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function interviewRounds(): HasMany
    {
        return $this->hasMany(InterviewRound::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ApplicationActivity::class)->latest();
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function canAdvance(): bool
    {
        return !in_array($this->stage, ['hired', 'rejected']);
    }

    public function nextStage(): ?string
    {
        $stages = array_keys(self::stages());
        $current = array_search($this->stage, $stages);
        if ($current === false || $current >= count($stages) - 3) {
            return null; // Don't auto-advance past offer
        }
        return $stages[$current + 1];
    }
}
