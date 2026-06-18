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

class LeaveController extends Controller
{
    /**
     * Create Leave Request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_type' => 'required|integer',
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

            $hierarchy = EmployeeHierarchy::where(
                'employee_id',
                $request->employee_id
            )->first();

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
                'status' => 2,
                'current_approver_id' => $firstApproverId,
            ]);

            LeaveHistory::create([
                'leave_id' => $leave->id,
                'actor_id' => auth()->user()->employee->id ?? $request->employee_id,
                'action' => 'pending',
                'note' => 'Leave applied. Awaiting approval from ' .
                    ($firstApproverId
                        ? optional(Employee::find($firstApproverId))->firstname . ' ' .
                          optional(Employee::find($firstApproverId))->lastname
                        : 'HR (no hierarchy set)')
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Leave applied successfully.',
                'data' => $leave
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
    public function show($id)
    {

   
        $leave = Leave::with([
            'employee',
            'currentApprover',
            'histories'
        ])->find($id);

        if (!$leave) {
            return response()->json([
                'status' => false,
                'message' => 'Leave record not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $leave
        ]);
    }

    /**
     * Get All Leaves
     */
    public function index()
    {
        $leaves = Leave::with([
            'employee',
            'currentApprover'
        ])
        ->latest()
        ->paginate(20);

        return response()->json([
            'status' => true,
            'data' => $leaves
        ]);
    }
}