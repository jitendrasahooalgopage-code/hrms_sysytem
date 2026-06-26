<?php

namespace App\Http\Controllers;

use App\Models\LeaveAllocation;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveAllocationController extends Controller
{
    public function index(Request $request)
    {
        // Default to current year if no explicit choice is passed from the calendar view selector
        $selectedYear = $request->get('year', date('Y'));
        
        $types = LeaveType::all();
        $allocations = LeaveAllocation::with('leaveType')
            ->where('year', $selectedYear)
            ->get();

        return view('admin.leave.allocation.index', compact('allocations', 'types', 'selectedYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|digits:4',
            'leave_type_id' => 'required|exists:leave_types,id',
            'days' => 'required|integer|min:0',
        ]);

        // Prevent duplicate allocations for the same leave type in a single year
        LeaveAllocation::updateOrCreate(
            ['year' => $validated['year'], 'leave_type_id' => $validated['leave_type_id']],
            ['days' => $validated['days']]
        );

        return redirect()->route('leave-allocation.index', ['year' => $validated['year']])
            ->with('success', 'Leaves allocated successfully.');
    }

    public function destroy($id)
    {
        $allocation = LeaveAllocation::findOrFail($id);
        $year = $allocation->year;
        $allocation->delete();

        return redirect()->route('leave-allocation.index', ['year' => $year])
            ->with('success', 'Allocation removed successfully.');
    }
}