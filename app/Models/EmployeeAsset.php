<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAsset extends Model
{
    protected $fillable = [
        'employee_id',
        'asset_name',
        'asset_details',
        'message',
        'assigned_date',
        'status'
    ];

    protected $casts = [
        'asset_details' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}