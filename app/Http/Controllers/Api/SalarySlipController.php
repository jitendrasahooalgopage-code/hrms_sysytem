<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalarySlip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // FIXED: Added missing Storage facade import
use Barryvdh\DomPDF\Facade\Pdf;

class SalarySlipController extends Controller
{
    /**
     * Get all salary slips for the authenticated employee with filtering.
     * GET /api/v1/salary-slips
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        if (!$employee) {   
            return response()->json([
                'success' => false,
                'message' => 'Employee profile record not found for this account.'
            ], 404);
        }

        $query = SalarySlip::where('employee_id', $employee->id);

        // Parameter filters mapping criteria
        if ($request->has('year') && !empty($request->year)) {
            $query->where('year', $request->year);
        }

        if ($request->has('month') && !empty($request->month)) {
            $query->where('month', $request->month);
        }

        $slips = $query->orderBy('year', 'desc')
            ->orderByRaw("FIELD(month, 'December', 'November', 'October', 'September', 'August', 'July', 'June', 'May', 'April', 'March', 'February', 'January')")
            ->get();

        $formattedSlips = $slips->map(function ($slip) {
            $is_accessible = $slip->status === 'Paid';

            $downloadUrl = null;

if ($is_accessible) {

    Storage::disk('public')->makeDirectory('payslips');

    $fileName = "payslip_{$slip->id}.pdf";

    // Generate only if it doesn't already exist
    if (!Storage::disk('public')->exists("payslips/{$fileName}")) {

        $pdf = Pdf::loadView('admin.salary.pdf', [
            'slip' => $slip,
        ]);

        Storage::disk('public')->put(
            "payslips/{$fileName}",
            $pdf->output()
        );
    }

    $downloadUrl = asset("storage/payslips/{$fileName}");
}

return [
    'id' => $slip->id,
    'month' => $slip->month,
    'year' => $slip->year,
    'pay_period' => "{$slip->month} {$slip->year}",
    'net_salary' => (float) $slip->net_salary,
    'net_salary_formatted' => '₹ ' . number_format($slip->net_salary, 2, '.', ','),
    'status' => $slip->status,
    'pay_date' => $slip->pay_date
        ? \Carbon\Carbon::parse($slip->pay_date)->format('Y-m-d')
        : null,
    'pay_date_formatted' => $slip->pay_date
        ? \Carbon\Carbon::parse($slip->pay_date)->format('d M Y')
        : 'Pending',
    'is_downloadable' => $is_accessible,
    'download_url' => $downloadUrl,
];
        });

        return response()->json([
            'success' => true,
            'message' => 'Salary slips retrieved successfully.',
            'data' => [
                'employee_id' => $employee->unique_id,
                'slips' => $formattedSlips
            ]
        ], 200);
    }
}