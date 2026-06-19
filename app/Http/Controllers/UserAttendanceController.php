<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class UserAttendanceController extends Controller
{
    /**
     * Record a check-in for the authenticated user.
     * A user can check in multiple times (each creates a new session row).
     */
    // public function checkin(Request $request): JsonResponse
    // {
    //     // dd($request->all());
    //     $validated = $request->validate([
    //         'latitude'  => 'required|numeric|between:-90,90',
    //         'longitude' => 'required|numeric|between:-180,180',
    //         'address'   => 'nullable|string|max:500',
    //         'accuracy'  => 'nullable|numeric|min:0',
    //     ]);

    //     $user  = Auth::user();
    //     $agent = new Agent();
    //     $agent->setUserAgent($request->userAgent());

    //     $ip = $request->header('X-Forwarded-For')
    //         ? explode(',', $request->header('X-Forwarded-For'))[0]
    //         : $request->ip();

    //     $log = AttendanceLog::create([
    //         'user_id'          => $user->id,
    //         'name'             => $user->name,
    //         'email'            => $user->email,
    //         'checkin_latitude' => $validated['latitude'],
    //         'checkin_longitude'=> $validated['longitude'],
    //         'checkin_address'  => $validated['address'] ?? null,
    //         'checkin_accuracy' => $validated['accuracy'] ?? null,
    //         'checkin_at'       => now(),
    //         'status'           => 'active',
    //         'ip_address'       => $ip,
    //         'device_type'      => $agent->isMobile() ? 'Mobile' : ($agent->isTablet() ? 'Tablet' : 'Desktop'),
    //         'browser'          => $agent->browser() ?: null,
    //         'platform'         => $agent->platform() ?: null,
    //         'user_agent'       => $request->userAgent(),
    //     ]);

    //     return response()->json([
    //         'success'    => true,
    //         'message'    => 'Checked in successfully.',
    //         'log_id'     => $log->id,
    //         'checkin_at' => $log->checkin_at->toDateTimeString(),
    //     ]);
    // }

    public function checkin(Request $request): JsonResponse
{

    $validated = $request->validate([

        'latitude'  => 'required|numeric|between:-90,90',

        'longitude' => 'required|numeric|between:-180,180',

        'address'   => 'nullable|string|max:500',

        'accuracy'  => 'nullable|numeric|min:0',

    ]);


    $officeLat = 20.282603;
    $officeLng = 85.857688;


    $distance = $this->calculateDistance(
        $validated['latitude'],
        $validated['longitude'],
        $officeLat,
        $officeLng
    );


    $checkinStatus = $distance <= 1000
        ? 'in-office'
        : 'out-office';



    $user = Auth::user();


    $agent = new Agent();

    $agent->setUserAgent($request->userAgent());



    $ip = $request->header('X-Forwarded-For')
        ? explode(',', $request->header('X-Forwarded-For'))[0]
        : $request->ip();



    $log = AttendanceLog::create([


        'user_id' => $user->id,

        'name' => $user->name,

        'email' => $user->email,


        'checkin_latitude' => $validated['latitude'],

        'checkin_longitude'=> $validated['longitude'],


        'checkin_address' => $validated['address'] ?? null,


        'checkin_accuracy'=> $validated['accuracy'] ?? null,


        'checkin_at'=> now(),


        'status'=>'active',


        'checkin_status'=>$checkinStatus,


        'ip_address'=>$ip,


        'device_type'=>$agent->isMobile()
            ? 'Mobile'
            : ($agent->isTablet()
                ? 'Tablet'
                : 'Desktop'),


        'browser'=>$agent->browser() ?: null,


        'platform'=>$agent->platform() ?: null,


        'user_agent'=>$request->userAgent(),

    ]);



    return response()->json([


        'success'=>true,


        'message'=>
            $checkinStatus == 'in-office'
            ? 'Checked in inside office.'
            : 'Checked in outside office.',


        'log_id'=>$log->id,


        'checkin_at'=>$log->checkin_at->toDateTimeString(),


        'distance'=>round($distance).' meters',


        'checkin_status'=>$checkinStatus,


    ]);

}

    /**
     * Record a check-out.
     * Finds the most recent active (unchecked-out) session for this user
     * and closes it with location + timestamp.
     */
    // public function checkout(Request $request): JsonResponse
    // {
    //     $validated = $request->validate([
    //         'latitude'  => 'required|numeric|between:-90,90',
    //         'longitude' => 'required|numeric|between:-180,180',
    //         'address'   => 'nullable|string|max:500',
    //         'accuracy'  => 'nullable|numeric|min:0',
    //     ]);

    //     $user = Auth::user();

    //     // Find the most recent active session for this user
    //     $log = AttendanceLog::where('user_id', $user->id)
    //         ->where('status', 'active')
    //         ->latest('checkin_at')
    //         ->first();

    //     if (! $log) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'No active check-in session found. Please check in first.',
    //         ], 422);
    //     }

    //     $checkoutAt      = now();
    //     $sessionDuration = (int) $checkoutAt->diffInSeconds($log->checkin_at);

    //     $log->update([
    //         'checkout_latitude'  => $validated['latitude'],
    //         'checkout_longitude' => $validated['longitude'],
    //         'checkout_address'   => $validated['address'] ?? null,
    //         'checkout_accuracy'  => $validated['accuracy'] ?? null,
    //         'checkout_at'        => $checkoutAt,
    //         'session_duration'   => $sessionDuration,
    //         'status'             => 'completed',
    //     ]);

    //     return response()->json([
    //         'success'          => true,
    //         'message'          => 'Checked out successfully.',
    //         'log_id'           => $log->id,
    //         'checkout_at'      => $log->checkout_at->toDateTimeString(),
    //         'session_duration' => $log->formatted_duration,
    //     ]);
    // }

    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address'   => 'nullable|string|max:500',
            'accuracy'  => 'nullable|numeric|min:0',
        ]);


        // Office Location
        $officeLat = 20.282603;
        $officeLng = 85.857688;


        // Calculate distance from office
        $distance = $this->calculateDistance(
            $validated['latitude'],
            $validated['longitude'],
            $officeLat,
            $officeLng
        );


        // 1000 meter radius check
        $checkoutStatus = $distance <= 1000
            ? 'in-office'
            : 'out-office';



        $user = Auth::user();



        // Find active check-in session
        $log = AttendanceLog::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest('checkin_at')
            ->first();



        if (! $log) {

            return response()->json([

                'success' => false,

                'message' => 'No active check-in session found. Please check in first.',

            ], 422);

        }




        $checkoutAt = now();


        $sessionDuration = (int)
            $checkoutAt->diffInSeconds($log->checkin_at);




        $log->update([


            'checkout_latitude'  => $validated['latitude'],


            'checkout_longitude' => $validated['longitude'],


            'checkout_address'   => $validated['address'] ?? null,


            'checkout_accuracy'  => $validated['accuracy'] ?? null,


            'checkout_at'        => $checkoutAt,


            'session_duration'   => $sessionDuration,


            'checkout_status'    => $checkoutStatus,


            'status'             => 'completed',


        ]);





        return response()->json([


            'success' => true,


            'message' => $checkoutStatus == 'in-office'

                ? 'Checked out inside office.'

                : 'Checked out outside office.',



            'log_id' => $log->id,


            'checkout_at' => $log->checkout_at->toDateTimeString(),



            'session_duration' => $log->formatted_duration,



            'checkout_status' => $checkoutStatus,



            'distance' => round($distance).' meters',


        ]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters


        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);

        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);



        $latDelta = $lat2 - $lat1;

        $lonDelta = $lon2 - $lon1;



        $a = sin($latDelta / 2) * sin($latDelta / 2)

            +

            cos($lat1)

            *

            cos($lat2)

            *

            sin($lonDelta / 2)

            *

            sin($lonDelta / 2);



        $c = 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );


        return $earthRadius * $c;
    }

    /**
     * Return the current active session for the authenticated user (if any).
     * Useful for showing "you are currently checked in" state in the UI.
     */
    public function status(): JsonResponse
    {
        $user       = Auth::user();
        $activeLog  = AttendanceLog::where('user_id', $user->id)
            ->active()
            ->latest('checkin_at')
            ->first();

        return response()->json([
            'is_checked_in' => (bool) $activeLog,
            'active_log'    => $activeLog ? [
                'id'              => $activeLog->id,
                'checkin_at'      => $activeLog->checkin_at->toDateTimeString(),
                'checkin_address' => $activeLog->checkin_address,
            ] : null,
        ]);
    }

    /**
     * Return paginated attendance history for the authenticated user.
     */
    public function history(Request $request): JsonResponse
    {
        $logs = AttendanceLog::where('user_id', Auth::id())
            ->latest('checkin_at')
            ->paginate(15);

        return response()->json($logs);
    }
}
