<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobPosition extends Model
{
    protected $fillable = [
        'title', 'department', 'location', 'type',
        'openings', 'is_active', 'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function types(): array
    {
        return [
            'full_time'  => 'Full Time',
            'part_time'  => 'Part Time',
            'contract'   => 'Contract',
            'internship' => 'Internship',
        ];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
