<?php

namespace App\Services;

use App\Models\UserAppNotificastion;
use App\Models\EmployeeNotification;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Resolve which users should receive a given notification.
     */
    public function resolveRecipients(UserAppNotificastion $notification): Collection
    {
        $query = User::query()->with('role');

        // Broadcast → everyone
        if ($notification->is_broadcast) {
            return $query->get();
        }

        // Specific employee IDs
        if (!empty($notification->target_employee_ids)) {
            return $query->whereIn('id', $notification->target_employee_ids)->get();
        }

        // Specific roles
        if (!empty($notification->target_roles)) {
            return $query->whereHas('role', function ($q) use ($notification) {
                $q->whereIn('slug', $notification->target_roles);
            })->get();
        }

        // Default: everyone
        return $query->get();
    }

    /**
     * Dispatch a notification to all resolved recipients.
     */
    public function dispatch(UserAppNotificastion $notification): array
    {
        try {
            $recipients = $this->resolveRecipients($notification);

            DB::transaction(function () use ($notification, $recipients) {
                // Build bulk insert data
                $rows = $recipients->map(fn(User $u) => [
                    'notification_id' => $notification->id,
                    'user_id'         => $u->id,
                    'is_read'         => false,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ])->toArray();

                // Chunk to avoid query size limits
                foreach (array_chunk($rows, 500) as $chunk) {
                    EmployeeNotification::insertOrIgnore($chunk);
                }

                $notification->update([
                    'status'  => 'sent',
                    'sent_at' => now(),
                ]);
            });

            Log::info("Notification #{$notification->id} dispatched to {$recipients->count()} recipients.");

            return [
                'success'         => true,
                'recipients_count' => $recipients->count(),
                'message'         => "Notification sent to {$recipients->count()} recipient(s).",
            ];
        } catch (\Throwable $e) {
            Log::error("Notification dispatch failed: {$e->getMessage()}");

            $notification->update(['status' => 'failed']);

            return [
                'success' => false,
                'message' => 'Notification dispatch failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create and immediately send a notification.
     */
    public function createAndSend(array $data, int $createdBy): array
    {
        $notification = UserAppNotificastion::create([
            ...$data,
            'status'     => 'draft',
            'created_by' => $createdBy,
        ]);

        return $this->dispatch($notification);
    }

    /**
     * Schedule a notification for future sending.
     */
    public function schedule(array $data, int $createdBy): UserAppNotificastion
    {
        return UserAppNotificastion::create([
            ...$data,
            'status'     => 'scheduled',
            'created_by' => $createdBy,
        ]);
    }

    /**
     * Process all due scheduled notifications (called from a cron job).
     */
    public function processScheduled(): void
    {
        UserAppNotificastion::dueForSending()->each(function (UserAppNotificastion $n) {
            $this->dispatch($n);
        });
    }

    /**
     * Get paginated notifications for a user with optional category filter.
     */
    public function forUser(User $user, ?string $category = null, int $perPage = 20)
    {
        $query = EmployeeNotification::with(['notification.creator'])
            ->where('user_id', $user->id)
            ->whereNull('dismissed_at')
            ->whereHas('notification', fn($q) => $q->where('status', 'sent'))
            ->orderByDesc('created_at');

        if ($category && $category !== 'ALL') {
            $query->whereHas('notification', fn($q) => $q->where('category', $category));
        }

        return $query->paginate($perPage);
    }

    /**
     * Count unread notifications for a user.
     */
    public function unreadCount(User $user): int
    {
        return EmployeeNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->whereNull('dismissed_at')
            ->whereHas('notification', fn($q) => $q->where('status', 'sent'))
            ->count();
    }
}