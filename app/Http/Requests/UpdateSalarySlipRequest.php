<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSalarySlipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'month'                => 'required|string',
            'year'                 => 'required|integer',
            'basic_salary'         => 'required|numeric|min:0',
            'house_rent_allowance' => 'required|numeric|min:0',
            'conveyance_allowance' => 'required|numeric|min:0',
            'medical_allowance'    => 'required|numeric|min:0',
            'special_allowance'    => 'required|numeric|min:0',
            'provident_fund'       => 'required|numeric|min:0',
            'professional_tax'     => 'required|numeric|min:0',
            'income_tax'           => 'required|numeric|min:0',
            'other_deductions'     => 'required|numeric|min:0',
            'gross_earnings'       => 'required|numeric|min:0',
            'total_deductions'     => 'required|numeric|min:0',
            'net_salary'           => 'required|numeric|min:0',
            'pay_date'             => 'nullable|date',
            'status'               => 'required|in:Paid,Pending',
            'remarks'              => 'nullable|string',
        ];
    }
}