<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalarySlip extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'basic_salary',
        'house_rent_allowance',
        'conveyance_allowance',
        'medical_allowance',
        'special_allowance',
        'provident_fund',
        'professional_tax',
        'income_tax',
        'other_deductions',
        'gross_earnings',
        'total_deductions',
        'net_salary',
        'pay_date',
        'status',
        'remarks',
    ];

    /**
     * Get the employee associated with the salary slip.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}