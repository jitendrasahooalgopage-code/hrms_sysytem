<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeHierarchy;
use App\Models\Leave;
use App\Models\LeaveHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $employees = Employee::all();

        return view('admin.leave.create', compact('employees'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => 'required|string|max:255',
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_type' => 'required|integer',
            'leave_reason' => 'nullable|string',
        ]);

        $hierarchy = EmployeeHierarchy::where('employee_id', $request->employee_id)->first();
        // dd($hierarchy);
        $firstApproverId = null;

        if ($hierarchy) {
            $firstApproverId = $hierarchy->team_lead_id
                            ?? $hierarchy->manager_id
                            ?? $hierarchy->hr_id;
        }
        // dd($firstApproverId);

        $leave = Leave::create([
            'employee_id' => $request->employee_id,
            'title' => $request->title,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'leave_type' => $request->leave_type,
            'leave_reason' => $request->leave_reason,
            'status' => 2, // pending
            'current_approver_id' => $firstApproverId,
        ]);

        // Log initial pending history
        LeaveHistory::create([
            'leave_id' => $leave->id,
            'actor_id' => auth()->user()->employee->id,
            'action' => 'pending',
            'note' => 'Leave applied. Awaiting approval from '.
                          ($firstApproverId
                              ? Employee::find($firstApproverId)?->firstname.' '.Employee::find($firstApproverId)?->lastname
                              : 'HR (no hierarchy set)'),
        ]);

        return back()->with('success', 'Leave applied successfully.');
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

        return view('admin.leave.edit', compact('leave', 'employees'));
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
