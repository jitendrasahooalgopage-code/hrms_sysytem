<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LeaveAllocation;

class LeaveType extends Model
{
    protected $fillable = ['name', 'code', 'description', 'is_active'];

    public function allocations()
    {
        return $this->hasMany(LeaveAllocation::class);
    }
}
