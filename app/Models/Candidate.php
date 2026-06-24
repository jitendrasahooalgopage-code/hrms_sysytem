<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    protected $fillable = [
        'name', 'email', 'phone',
        'cv_path', 'cv_original_name',
        'linkedin_url', 'portfolio_url', 'notes',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', $this->name);
        return strtoupper(
            count($parts) >= 2
                ? $parts[0][0] . $parts[1][0]
                : substr($this->name, 0, 2)
        );
    }

    public function hasCv(): bool
    {
        return !empty($this->cv_path);
    }
}
