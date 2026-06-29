<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeHierarchy;
use App\Models\Leave;
use App\Models\LeaveHistory;
use Illuminate\Support\Facades\Validator;
use Exception;
    use App\Models\EmployeeLeaveAllocation;


class LeaveController extends Controller
{
    /**
     * Create Leave Request
     */
   /**
     * Create Leave Request with Real-Time Balance Validation
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_type' => 'required|integer|exists:leave_types,id', // Validates against leave_types table
            'leave_reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // 1. Calculate total requested calendar days (inclusive)
            $startDate = \Carbon\Carbon::parse($request->start_date);
            $endDate = \Carbon\Carbon::parse($request->end_date);
            $requestedDays = $startDate->diffInDays($endDate) + 1; 

            $year = $startDate->format('Y');

            // 2. Fetch the employee's live leave allocation balance bucket for this specific type & year
            $allocation = EmployeeLeaveAllocation::where([
                'employee_id' => $request->employee_id,
                'leave_type_id' => $request->leave_type,
                'year' => $year
            ])->first();

            // Industry Standard Check: Prevent applying if no allocation was pushed to this profile
            if (!$allocation) {
                return response()->json([
                    'status' => false,
                    'message' => 'Leave policy allocation mismatch. This leave type has not been explicitly assigned to your profile for ' . $year . ' yet.'
                ], 422);
            }

            // 3. Sufficiency Check: Ensure requested days do not exceed remaining allowance balance
            if ($allocation->remaining_days < $requestedDays) {
                return response()->json([
                    'status' => false,
                    'message' => sprintf(
                        'Insufficient leave balance. You requested %d days, but your remaining balance for this leave type is only %d days.',
                        $requestedDays,
                        $allocation->remaining_days
                    )
                ], 422);
            }

            // --- ALL GATES PASSED: PROCEED TO RECORD SAVING ---
            $hierarchy = EmployeeHierarchy::where('employee_id', $request->employee_id)->first();
            $firstApproverId = null;

            if ($hierarchy) {
                $firstApproverId = $hierarchy->team_lead_id
                    ?? $hierarchy->manager_id
                    ?? $hierarchy->hr_id;
            }

            $leave = Leave::create([
                'employee_id' => $request->employee_id,
                'title' => $request->title,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'leave_type' => $request->leave_type,
                'leave_reason' => $request->leave_reason,
                'status' => 2, // Pending
                'current_approver_id' => $firstApproverId,
            ]);

            LeaveHistory::create([
                'leave_id' => $leave->id,
                'actor_id' => auth()->user()->employee->id ?? $request->employee_id,
                'action' => 'pending',
                'note' => 'Leave applied for ' . $requestedDays . ' day(s). Awaiting approval from ' .
                    ($firstApproverId
                        ? optional(Employee::find($firstApproverId))->firstname . ' ' . optional(Employee::find($firstApproverId))->lastname
                        : 'HR (no hierarchy set)')
            ]);

            $leaveTypeObj = \App\Models\LeaveType::find($request->leave_type);

            return response()->json([
                'status' => true,
                'message' => 'Leave applied successfully and validated against your active allocation profile.',
                'data' => [
                    'id' => $leave->id,
                    'employee_id' => (int) $leave->employee_id,
                    'title' => $leave->title,
                    'start_date' => $leave->start_date,
                    'end_date' => $leave->end_date,
                    'leave_type' => (int) $leave->leave_type,
                    'leave_type_name' => $leaveTypeObj ? $leaveTypeObj->name : 'Unknown', // Dynamic name fixed
                    'leave_type_code' => $leaveTypeObj ? $leaveTypeObj->code : '',       // Dynamic code fixed
                    'leave_reason' => $leave->leave_reason,
                    'status' => (int) $leave->status,
                    'current_approver_id' => $leave->current_approver_id ? (int) $leave->current_approver_id : null,
                    'created_at' => $leave->created_at,
                    'updated_at' => $leave->updated_at,
                ]
            ], 201);          

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create leave.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Leave
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_type' => 'required|integer',
            'status' => 'nullable|integer',
            'leave_reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $leave = Leave::find($id);

            if (!$leave) {
                return response()->json([
                    'status' => false,
                    'message' => 'Leave record not found'
                ], 404);
            }

            $leave->update([
                'title' => $request->title,
                'employee_id' => $request->employee_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'leave_type' => $request->leave_type,
                'status' => $request->status,
                'leave_reason' => $request->leave_reason,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Leave record updated successfully.',
                'data' => $leave->fresh()
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Failed to update leave.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete Leave
     */
    public function destroy($id)
    {
        try {

            $leave = Leave::find($id);

            if (!$leave) {
                return response()->json([
                    'status' => false,
                    'message' => 'Leave record not found'
                ], 404);
            }

            $leave->delete();

            return response()->json([
                'status' => true,
                'message' => 'Leave deleted successfully.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Failed to delete leave.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Single Leave
     */
   /**
     * Get Leaves with Role-Based Scope Visibility.
     * GET /api/v1/leaves
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        // Base Query Builder with relationships eager-loaded
        $query = Leave::with(['employee', 'currentApprover'])->latest();

        // If the user has a role, check its slug. (Adjust 'slug' or 'title' to match your Role model)
        $roleSlug = $user->role->slug ?? 'employee';

        // INDUSTRY STANDARD SECURITY GATE: Non-management profiles only see their own rows
        if (!in_array($roleSlug, ['super-admin', 'hr', 'administrator', 'manager', 'team-lead'])) {
            if (!$employee) {
                return response()->json([
                    'status' => false,
                    'message' => 'Employee profile record missing.'
                ], 404);
            }
            $query->where('employee_id', $employee->id);
        }

        $leaves = $query->paginate(20);

        return response()->json([
            'status' => true,
            'data' => $leaves
        ], 200);
    }

    /**
     * Get Single Leave Details with Ownership Protection.
     * GET /api/v1/leaves/{id}
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $employee = $user->employee;
        $roleSlug = $user->role->slug ?? 'employee';

        $leave = Leave::with([
            'employee',
            'currentApprover',
            'histories.actor'
        ])->find($id);

        if (!$leave) {
            return response()->json([
                'status' => false,
                'message' => 'Leave record not found'
            ], 404);
        }

        // SECURITY MULTI-GATE: Block if it's a regular employee and they don't own this specific request
        if (!in_array($roleSlug, ['super-admin', 'hr', 'administrator', 'manager', 'team-lead'])) {
            if (!$employee || $leave->employee_id !== $employee->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access to this leave record request.'
                ], 403);
            }
        }

        return response()->json([
            'status' => true,
            'data' => $leave
        ], 200);
    }


/**
 * Get assigned leave balances and quotas for a specific employee.
 * GET /api/v1/employees/{id}/leaves
 */
public function getMyLeaveTypes(Request $request)
{
    $user = $request->user();
    
    // Lazy load the employee profile along with department and designation metadata
    $employee = $user->employee;

    if (!$employee) {
        return response()->json([
            'success' => false,
            'message' => 'Employee profile record not found for this account.'
        ], 404);
    }

    $currentYear = $request->get('year', date('Y'));

    // Fetch leave allocations for this exact employee from the token context
    $leaveAllocations = EmployeeLeaveAllocation::with('leaveType')
        ->where('employee_id', $employee->id)
        ->where('year', $currentYear)
        ->get();

    // Map and format the leave entitlements array
    $formattedBalances = $leaveAllocations->map(function ($alloc) {
        return [
            'leave_type_id' => $alloc->leave_type_id,
            'leave_name' => $alloc->leaveType->name,
            'leave_code' => $alloc->leaveType->code,
            'allocated_days' => (int) $alloc->allocated_days,
            'used_days' => (int) $alloc->used_days,
            'remaining_days' => (int) $alloc->remaining_days,
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'Employee profile and leave matrix fetched successfully.',
        'data' => [
            // Return Employee Core Profile Metadata Info Block
            'employee' => [
                'id' => $employee->id,
                'unique_id' => $employee->unique_id,
                'firstname' => $employee->firstname,
                'lastname' => $employee->lastname,
                'email' => $employee->email,
                'department' => $employee->department?->title ?? 'N/A',
                'designation' => $employee->designation?->title ?? 'N/A',
                'date_of_joining' => $employee->doj ?? 'N/A',
            ],
            'fiscal_year' => (int) $currentYear,
            'summary' => [
                'total_leave_types' => $formattedBalances->count(),
                'total_allotted_quota' => $formattedBalances->sum('allocated_days'),
                'total_availed' => $formattedBalances->sum('used_days'),
                'total_available_balance' => $formattedBalances->sum('remaining_days'),
            ],
            'entitlements' => $formattedBalances
        ]
    ], 200);
}
}