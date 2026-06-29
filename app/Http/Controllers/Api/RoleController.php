<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;

class RoleController extends Controller
{
    /**
     * Get a lightweight list of only active role titles for dropdown lists.
     * GET /api/v1/roles-list
     */
    public function getTitlesList()
    {
        // Pluck only the id and title from active roles (status = 1)
        $roles = \App\Models\Role::where('status', 1)
            ->orderBy('title', 'asc')
            ->get(['id', 'title']); 

        return response()->json([
            'success' => true,
            'message' => 'Lightweight roles list fetched successfully.',
            'data' => $roles
        ], 200);
    }
}
