<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $types = LeaveType::orderBy('name')->get();
        return view('admin.leaves.types.index', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:leave_types,code',
            'description' => 'nullable|string'
        ]);

        LeaveType::create($validated);
        return redirect()->back()->with('success', 'Leave Type registered successfully.');
    }

    public function update(Request $request, $id)
    {
        $type = LeaveType::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:leave_types,code,' . $id,
            'description' => 'nullable|string'
        ]);

        $type->update($validated);
        return redirect()->back()->with('success', 'Leave Type updated successfully.');
    }

    public function destroy($id)
    {
        LeaveType::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Leave Type deleted successfully.');
    }
}