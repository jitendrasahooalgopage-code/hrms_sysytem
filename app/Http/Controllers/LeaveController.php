<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeHierarchy;
use App\Models\Leave;
use App\Models\LeaveHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveType;
use App\Models\EmployeeLeaveAllocation;
use Carbon\Carbon;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $loggedInEmployeeId = $user->employee->id ?? null;

        $query = Leave::with(['employee', 'currentApprover', 'histories.actor'])
            ->latest();

        // If role is employee, show only their leaves
        if ($user->role->slug === 'employee') {
            $query->where('employee_id', $loggedInEmployeeId);
        }

        $leaves = $query->get();

        return view('admin.leave.index', compact('leaves', 'loggedInEmployeeId'));
    }

    /**
     * Show the form for creating a new resource.
     */
   public function create()
    {
        $user = auth()->user();
        $employees = Employee::all();
        
        // Fetch leave types that have active allocations for the employee or globally
        $leaveTypes = LeaveType::where('is_active', true)->get();

        return view('admin.leave.create', compact('employees', 'leaveTypes'));
    }

    /**
     * Store a newly created leave request with dynamic quota validation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_type' => 'required|exists:leave_types,id', // Validates against dynamic table IDs
            'leave_reason' => 'nullable|string',
        ]);

        try {
            // 1. Calculate requested total calendar days (inclusive)
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $requestedDays = $startDate->diffInDays($endDate) + 1;

            $year = $startDate->format('Y');

            // 2. Fetch the specific employee's leave allocation bucket
            $allocation = EmployeeLeaveAllocation::where([
                'employee_id' => $request->employee_id,
                'leave_type_id' => $request->leave_type,
                'year' => $year
            ])->first();

            // Guard Clause: Check if policy was pushed to this profile
            if (!$allocation) {
                return back()->withInput()->withErrors([
                    'leave_type' => "This leave type category has not been explicitly assigned to the selected employee profile for the year {$year}."
                ]);
            }

            // 3. Sufficiency Check: Ensure remaining balance covers request
            if ($allocation->remaining_days < $requestedDays) {
                return back()->withInput()->withErrors([
                    'leave_type' => "Insufficient leave balance. Request requires {$requestedDays} days, but only {$allocation->remaining_days} days are remaining."
                ]);
            }

            // --- ALL POLICY GATES PASSED: PROCESS HIERARCHY CHAIN ---
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

            // Log initial pending history
            LeaveHistory::create([
                'leave_id' => $leave->id,
                'actor_id' => auth()->user()->employee->id ?? $request->employee_id,
                'action' => 'pending',
                'note' => "Leave applied for {$requestedDays} day(s). Awaiting approval from " .
                              ($firstApproverId
                                  ? Employee::find($firstApproverId)?->firstname . ' ' . Employee::find($firstApproverId)?->lastname
                                  : 'HR (no hierarchy set)'),
            ]);

            return back()->with('success', 'Leave applied successfully and validated against allocation quotas.');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'System processing error: ' . $e->getMessage()]);
        }
    }

  

    /**
     * Approve a leave — moves it up the chain automatically.
     */
    public function approve(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);
        $approverId = auth()->user()->employee->id ?? null;
        $approver = Employee::find($approverId);

       
        if (! $approverId || $leave->current_approver_id !== $approverId) {
            return back()->with('error', 'You are not authorized to approve this leave.');
        }

        
        if (in_array($leave->status, [1, 4])) {
            return back()->with('error', 'This leave has already been finalized.');
        }

        $hierarchy = EmployeeHierarchy::where('employee_id', $leave->employee_id)->first();

        // No hierarchy set — approve directly
        if (! $hierarchy) {
            $leave->update(['status' => 1, 'current_approver_id' => null]);

            LeaveHistory::create([
                'leave_id' => $leave->id,
                'actor_id' => $approverId,
                'action' => 'approved',
                'note' => 'Fully approved by '.$approver->firstname.' '.$approver->lastname,
            ]);

            return back()->with('success', 'Leave fully approved.');
        }

        // Determine next approver based on who is acting
        if ($approverId === $hierarchy->team_lead_id) {
            $next = $hierarchy->manager_id ?? $hierarchy->hr_id;
            $status = 2; // approved_by_team_lead
            $role = 'Team Lead';
        } elseif ($approverId === $hierarchy->manager_id) {
            $next = $hierarchy->hr_id;
            $status = 3; // approved_by_manager
            $role = 'Manager';
        } else {
            // HR — final approver
            $next = null;
            $status = 1;
            $role = 'HR';
        }

        $nextEmployee = $next ? Employee::find($next) : null;
        $isFinal = $next === null;

        $leave->update([
            'status' => $isFinal ? 1 : $status,
            'current_approver_id' => $next,
        ]);

        // Log history
        LeaveHistory::create([
            'leave_id' => $leave->id,
            'actor_id' => $approverId,
            'action' => $isFinal ? 'approved' : 'forwarded',
            'note' => $isFinal
                ? 'Fully approved by '.$approver->firstname.' '.$approver->lastname.' ('.$role.')'
                : 'Approved by '.$approver->firstname.' '.$approver->lastname.' ('.$role.'). Forwarded to '.$nextEmployee?->firstname.' '.$nextEmployee?->lastname,
        ]);

        $msg = $isFinal
            ? 'Leave fully approved.'
            : 'Leave approved and forwarded to '.$nextEmployee?->firstname.' '.$nextEmployee?->lastname.'.';

        return back()->with('success', $msg);
    }

    /**
     * Reject a leave — any approver in the chain can reject.
     */
    public function reject(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);
        $approverId = auth()->user()->employee->id ?? null;
        $approver = Employee::find($approverId);

        // Auth check
        if (! $approverId || $leave->current_approver_id !== $approverId) {
            return back()->with('error', 'You are not authorized to reject this leave.');
        }

        // Already finalized
        if (in_array($leave->status, [0, 4])) {
            return back()->with('error', 'This leave has already been finalized.');
        }

        // Determine role for note
        $hierarchy = EmployeeHierarchy::where('employee_id', $leave->employee_id)->first();
        $role = 'HR';
        if ($hierarchy) {
            if ($approverId === $hierarchy->team_lead_id) {
                $role = 'Team Lead';
            } elseif ($approverId === $hierarchy->manager_id) {
                $role = 'Manager';
            }
        }

        $leave->update([
            'status' => 0,
            'current_approver_id' => null,
        ]);

        LeaveHistory::create([
            'leave_id' => $leave->id,
            'actor_id' => $approverId,
            'action' => 'rejected',
            'note' => 'Rejected by '.$approver->firstname.' '.$approver->lastname.' ('.$role.')',
        ]);

        return back()->with('success', 'Leave rejected.');
    }

    public function edit($id)
{
    $leave = Leave::with('histories.actor')->findOrFail($id);
    $employees = Employee::all();
    
    // Fetch all active leave type configuration models from the database
    $leaveTypes = LeaveType::where('is_active', true)->get();

    return view('admin.leave.edit', compact('leave', 'employees', 'leaveTypes')); // Added leaveTypes here
}

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_type' => 'required|integer',
            'status' => 'required|integer',
        ]);

        $leave = Leave::findOrFail($id);
        $leave->update($request->only([
            'title', 'employee_id', 'start_date',
            'end_date', 'leave_type', 'status', 'leave_reason',
        ]));

        return back()->with('success', 'Leave record updated successfully.');
    }

    public function destroy($id)
    {
        Leave::findOrFail($id)->delete();

        return redirect()->route('leaves.index')->with('success', 'Leave deleted successfully.');
    }
}
