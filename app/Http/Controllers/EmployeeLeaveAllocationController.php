<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveAllocation;
use App\Models\LeaveType;
use App\Models\EmployeeLeaveAllocation;
use Illuminate\Http\Request;

class EmployeeLeaveAllocationController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = $request->get('year', date('Y'));
        
        // Load employees with current allocations for this specific year
        $employees = Employee::with(['department', 'leaveAllocations' => function($query) use ($selectedYear) {
            $query->where('year', $selectedYear)->with('leaveType');
        }])->get();

        // Get the available global leave policy rules for this year
        $globalPolicies = LeaveAllocation::with('leaveType')->where('year', $selectedYear)->get();

        return view('admin.leave.assignments.index', compact('employees', 'globalPolicies', 'selectedYear'));
    }

    /**
     * Industry Standard Multi-Employee and Multi-Leave Type Assignment Engine
     */
    public function assignPolicy(Request $request)
    {
        $validated = $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'leave_type_ids' => 'required|array',
            'leave_type_ids.*' => 'exists:leave_types,id',
            'year' => 'required|digits:4'
        ]);

        $employeeIds = $validated['employee_ids'];
        $leaveTypeIds = $validated['leave_type_ids'];
        $year = $validated['year'];

        // Get the configuration rules for the selected leave types for this year
        $policies = LeaveAllocation::where('year', $year)
            ->whereIn('leave_type_id', $leaveTypeIds)
            ->get();

        if ($policies->isEmpty()) {
            return redirect()->back()->with('error', "No day quotas have been configured under Leave Quota Setup for the selected leave types in year {$year}.");
        }

        $processedCount = 0;

        foreach ($employeeIds as $employeeId) {
            foreach ($policies as $policy) {
                // Read existing record if any to preserve historical utilization
                $existingAllocation = EmployeeLeaveAllocation::where([
                    'employee_id' => $employeeId,
                    'leave_type_id' => $policy->leave_type_id,
                    'year' => $year
                ])->first();

                $usedDays = $existingAllocation ? $existingAllocation->used_days : 0;
                $allocatedDays = $policy->days;
                $remainingDays = $allocatedDays - $usedDays;

                EmployeeLeaveAllocation::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'leave_type_id' => $policy->leave_type_id,
                        'year' => $year
                    ],
                    [
                        'allocated_days' => $allocatedDays,
                        'used_days' => $usedDays,
                        'remaining_days' => $remainingDays
                    ]
                );
            }
            $processedCount++;
        }

        return redirect()->back()->with('success', "Successfully assigned/synchronized balances across {$processedCount} employee profiles.");
    }
}