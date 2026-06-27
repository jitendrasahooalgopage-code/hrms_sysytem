<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoginActivity;
use App\Models\Leave;
use App\Models\Attendance;
use Carbon\Carbon;

class RecentActivityController extends Controller
{
    /**
     * Fetch a unified timeline stream of recent activities for the logged-in user.
     * GET /api/v1/recent-activities
     */
    public function getRecentActivities(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;
        $employeeId = $employee ? $employee->id : null;

        $timeline = collect();

        // 1. Collect Recent Authentication Logs
        $logins = LoginActivity::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        foreach ($logins as $login) {
            $timeline->push([
                'id' => 'auth_' . $login->id,
                'type' => 'authentication',
                'title' => ucwords(str_replace('_', ' ', $login->activity_type)),
                'description' => "Logged in via {$login->login_method} using {$login->browser} on {$login->platform}.",
                'status' => $login->status, // success / failed
                'raw_status' => $login->status,
                'created_at' => $login->created_at->toIso8601String(),
                'time_ago' => $login->created_at->diffForHumans(),
            ]);
        }

        // 2. Collect Recent Leave Applications
        if ($employeeId) {
            $leaves = Leave::where('employee_id', $employeeId)
                ->latest()
                ->take(10)
                ->get();

            foreach ($leaves as $leave) {
                // Parse human-readable status mapping
                $statusText = match((int)$leave->status) {
                    0 => 'Rejected',
                    1 => 'Approved',
                    2 => 'Pending',
                    3 => 'Approved by Manager',
                    4 => 'Fully Approved',
                    default => 'Unknown',
                };

                $timeline->push([
                    'id' => 'leave_' . $leave->id,
                    'type' => 'leave',
                    'title' => "Leave Request: {$leave->title}",
                    'description' => "Applied for leave from " . Carbon::parse($leave->start_date)->format('d M') . " to " . Carbon::parse($leave->end_date)->format('d M') . ".",
                    'status' => $statusText,
                    'raw_status' => $leave->status,
                    'created_at' => $leave->created_at->toIso8601String(),
                    'time_ago' => $leave->created_at->diffForHumans(),
                ]);
            }
        }

        // 3. Collect Recent Attendance Swipes
        if ($employeeId) {
            $attendances = Attendance::where('employee_id', $employeeId)
                ->latest()
                ->take(10)
                ->get();

            foreach ($attendances as $attendance) {
                $timeline->push([
                    'id' => 'attendance_' . $attendance->id,
                    'type' => 'attendance',
                    'title' => "Attendance " . ucfirst($attendance->type ?? 'Check'), // check-in / check-out
                    'description' => "Marked via kiosk at {$attendance->attendance_time} on " . Carbon::parse($attendance->attendance_date)->format('d M Y') . ".",
                    'status' => $attendance->state ?? 'Success',
                    'raw_status' => $attendance->status,
                    'created_at' => $attendance->created_at->toIso8601String(),
                    'time_ago' => $attendance->created_at->diffForHumans(),
                ]);
            }
        }

        // 4. Sort everything chronologically by newest activity first
        $sortedTimeline = $timeline->sortByDesc('created_at')->values()->take(15);

        return response()->json([
            'success' => true,
            'message' => 'Recent timeline activity records retrieved successfully.',
            'count' => $sortedTimeline->count(),
            'data' => $sortedTimeline
        ], 200);
    }
}