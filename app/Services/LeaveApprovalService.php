<?php

use App\Models\Employee;
use App\Models\HierarchyLevel;
use App\Models\Leave;



class LeaveApprovalService
{
    public function approve(Leave $leave, Employee $approver): void
    {
        $dept = $leave->employee->department_id;

        // Find the next hierarchy level above current
        $nextLevel = HierarchyLevel::where('department_id', $dept)
            ->where('level', '>', $leave->current_level)
            ->orderBy('level')
            ->first();

        if ($nextLevel) {
            // Find the employee at that level in the same department
            $nextApprover = Employee::whereHas('user.role', function ($q) use ($nextLevel) {
                $q->where('id', $nextLevel->role_id);
            })->where('department_id', $dept)->first();

            $leave->update([
                'current_approver_id' => $nextApprover->id,
                'current_level'       => $nextLevel->level,
                'status'              => 'pending',
            ]);
        } else {
            // No higher level — final approval
            $leave->update(['status' => 'approved', 'current_approver_id' => null]);
        }
    }

    public function reject(Leave $leave): void
    {
        $leave->update(['status' => 'rejected', 'current_approver_id' => null]);
    }
}