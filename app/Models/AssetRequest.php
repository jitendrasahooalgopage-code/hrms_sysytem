<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetRequest extends Model
{
    protected $fillable = [
        'employee_id',
        'employee_asset_id',
        'request_type',
        'subject',
        'message',
        'photos',
        'status',
        'admin_remark',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'photos' => 'array',
        'approved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function asset()
    {
        return $this->belongsTo(
            EmployeeAsset::class,
            'employee_asset_id'
        );
    }

    public function approver()
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }
}