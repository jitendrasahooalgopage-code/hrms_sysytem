<?php
// app/Models/EmployeeHierarchy.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeHierarchy extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'team_lead_id', 'manager_id', 'hr_id'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function teamLead(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'team_lead_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function hr(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'hr_id');
    }
}