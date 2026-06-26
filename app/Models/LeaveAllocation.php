<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LeaveType;

class LeaveAllocation extends Model
{
    protected $fillable = ['year', 'leave_type_id', 'days'];

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}
