<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Services\LoginActivityService;

class AuthController extends Controller
{
    public function login(
        Request $request,
        LoginActivityService $loginActivityService
    ) {

        if (empty($request->email)) {
            return response()->json([
                "message" => "please enter your email!"
            ], 422);
        }

        if (empty($request->password)) {
            return response()->json([
                "message" => "please enter your password"
            ], 422);
        }

        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        $user = User::where("email", $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {

            // Optional: store failed login attempt
            if ($user) {
                $loginActivityService->store(
                    $user,
                    $request,
                    'failed_login',
                    'failed',
                    'Sanctum API'
                );
            }

            throw ValidationException::withMessages([
                'email' => ['Invalid credentials']
            ]);
        }

        // Create Sanctum Token
        $token = $user->createToken('auth-token')->plainTextToken;

        // Store Login Activity
        $loginActivityService->store(
            $user,
            $request,
            'login',
            'success',
            'Sanctum API'
        );

        return response()->json([
            'token' => $token,
            'user' => $user,
            'message' => 'Login successful'
        ]);
    }

    public function logout(
        Request $request,
        LoginActivityService $loginActivityService
    ) {

        $user = $request->user();

        if ($user) {

            $loginActivityService->store(
                $user,
                $request,
                'logout',
                'success',
                'Sanctum API'
            );

            $request->user()->currentAccessToken()?->delete();
        }

        return response()->json([
            "message" => "Logged out successfully"
        ]);
    }

    public function profile(Request $request)
    {
        if (!empty($request->user())) {

           $user = User::leftJoin(
        'employees',
        'employees.user_id',
        '=',
        'users.id'
    )
    ->leftJoin(
        'departments',
        'departments.id',
        '=',
        'employees.department_id'
    )
    ->leftJoin(
        'designations',
        'designations.id',
        '=',
        'employees.designation_id'
    )
    ->leftJoin(
        'schedules',
        'schedules.id',
        '=',
        'employees.schedule_id'
    )
    ->where(
        'users.email',
        $request->user()->email
    )
    ->select(
        'users.id',
        'users.email',
        'users.name',
        'users.phone',
        'employees.*',
        'departments.title as department_name',
        'designations.title as designation_name',
        'schedules.title as schedule_name'
    )
    ->first();

            return response()->json([
                'user' => $user,
                'message' => 'user information found!'
            ]);
        }

        return response()->json([
            'message' => 'no user info available'
        ], 404);
    }
    public function getAllEmployees(Request $request)
    {
        

            $user = User::leftJoin(
                'employees',
                'employees.user_id',
                '=',
                'users.id'
            )
            ->where(
                'users.email',
                $request->user()->email
            )
            ->orWhere(
                'employees.emp_status','=','active'
            )
            ->select(
                'users.id',
                'users.email',
                'users.name',
                'users.phone',
                'employees.*',
                
            )
            ->get()->toArray();

            return response()->json([
                'user' => $user,
                'message' => 'user information found!'
            ]);
        

       
    }
}