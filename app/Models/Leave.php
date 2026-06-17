<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [ 'employee_id', 'title', 'start_date','end_date', 'leave_type', 'leave_reason', 
    
    
    'status',               // 0=rejected, 1=pending, 2=approved_by_team_lead, 3=approved_by_manager, 4=approved
    'current_approver_id'
    
    
    ];

    public function employee(): BelongsTo {
        return $this->belongsTo(Employee::class);
    }
    public function currentApprover(): BelongsTo {
        return $this->belongsTo(Employee::class, 'current_approver_id');
    }
    public function histories(): HasMany {
        return $this->hasMany(LeaveHistory::class)->latest();
    }
}
