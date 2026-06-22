<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\EmployeeNotification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppNotificationController extends Controller
{
    public function __construct(private NotificationService $service) {}

    /**
     * Retrieve Active Mobile User Notification Feed
     */
    public function index(Request $request): JsonResponse
    {
        $category = $request->query('category');
        $perPage  = (int) $request->query('per_page', 20);
        $user     = $request->user();

        $paginatedData = $this->service->forUser($user, $category, $perPage);

        return response()->json([
            'success' => true,
            'data'    => NotificationResource::collection($paginatedData->items()),
            'meta'    => [
                'current_page' => $paginatedData->currentPage(),
                'last_page'    => $paginatedData->lastPage(),
                'per_page'     => $paginatedData->perPage(),
                'total'        => $paginatedData->total(),
                'has_more'     => $paginatedData->hasMorePages(),
            ]
        ]);
    }

    /**
     * Get Real-time Unread Metric Counters
     */
    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'success'      => true,
            'unread_count' => $this->service->unreadCount($request->user())
        ]);
    }

    /**
     * Track Client System Push Delivery Confirmation (Device Reception Auditing)
     * Payload Input: {"notification_ids": [10, 11, 14]}
     */
    public function trackDelivery(Request $request): JsonResponse
    {
        $request->validate([
            'notification_ids'   => ['required', 'array'],
            'notification_ids.*' => ['required', 'integer']
        ]);

        // Audit check that rows are explicitly targeted to the authenticated mobile bearer token profile
        EmployeeNotification::where('user_id', $request->user()->id)
            ->whereIn('notification_id', $request->input('notification_ids'))
            ->whereNull('created_at') 
            ->update(['created_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Device delivery receipt timestamps synchronized successfully.'
        ]);
    }

    /**
     * Mark Notification Payload Instance as READ
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $notification = EmployeeNotification::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        if (!$notification->is_read) {
            $notification->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Read status tracked successfully.'
        ]);
    }

    /**
     * Track Mobile App Swiped/Dismissed Events
     */
    public function dismiss(Request $request, int $id): JsonResponse
    {
        $notification = EmployeeNotification::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        $notification->update([
            'dismissed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification tracked as dismissed.'
        ]);
    }

    /**
     * Bulk Mark Entire Active Feed Collection to READ Status
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        EmployeeNotification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->whereNull('dismissed_at')
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All active notifications tracked as read.'
        ]);
    }
}