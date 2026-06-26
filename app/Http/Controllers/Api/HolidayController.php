<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Carbon\Carbon;

class HolidayController extends Controller
{
    /**
     * Display all active holidays.
     */
    public function index()
    {
        $holidays = Holiday::where('status', 1)
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(function ($holiday) {

                return [
                    'id' => $holiday->id,
                    'name' => $holiday->name,
                    'start_date' => $holiday->start_date->format('Y-m-d'),
                    'end_date' => $holiday->end_date->format('Y-m-d'),
                    'no_of_days' => $holiday->no_of_days,
                    'description' => $holiday->description,
                    'status' => $holiday->status,

                    // Additional fields for frontend
                    'date_range' => $holiday->start_date->equalTo($holiday->end_date)
                        ? $holiday->start_date->format('d M Y')
                        : $holiday->start_date->format('d M Y') . ' - ' . $holiday->end_date->format('d M Y'),

                    'is_upcoming' => Carbon::today()->lte($holiday->end_date),
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Holiday list fetched successfully.',
            'data' => $holidays
        ]);
    }
}