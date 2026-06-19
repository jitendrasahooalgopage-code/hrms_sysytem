<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = AttendanceLog::with('user')->latest('checkin_at');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('checkin_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('checkin_at', '<=', $request->date_to);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name',  'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $logs  = $query->paginate(20)->withQueryString();
        $users = User::orderBy('name')->get(['id', 'name']);

        // Stats for top cards
        $stats = [
            'total'     => AttendanceLog::count(),
            'active'    => AttendanceLog::where('status', 'active')->count(),
            'completed' => AttendanceLog::where('status', 'completed')->count(),
            'today'     => AttendanceLog::whereDate('checkin_at', today())->count(),
        ];

        return view('admin.attendance-list.index', compact('logs', 'users', 'stats'));
    }

    // ── Show single log detail ───────────────────────────────────────────
    public function show(AttendanceLog $attendance_log)
    {
        $attendance_log->load('user');
        return view('admin.attendance-list.show', compact('attendance_log'));
    }

    // ── Delete a log ─────────────────────────────────────────────────────
    public function destroy(AttendanceLog $attendance_log)
    {
        $attendance_log->delete();
        return back()->with('success', 'Attendance log deleted successfully.');
    }

    // ── Force checkout ───────────────────────────────────────────────────
    public function forceCheckout(AttendanceLog $attendance_log)
    {
        if ($attendance_log->status === 'active') {
            $now      = now();
            $duration = $now->diffInSeconds($attendance_log->checkin_at);
            $attendance_log->update([
                'checkout_at'      => $now,
                'session_duration' => $duration,
                'status'           => 'completed',
            ]);
        }
        return back()->with('success', 'Force checkout done.');
    }

   



}
