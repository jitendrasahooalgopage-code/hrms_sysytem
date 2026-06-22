<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\UserAppNotificastion;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // $this contextual instance references the EmployeeNotification pivot model record
        return [
            'tracking_id'   => $this->id,
            'user_id'       => $this->user_id,
            'is_read'       => (bool) $this->is_read,
            'read_at'       => $this->read_at ? $this->read_at->toIso8601String() : null,
            'delivered_at'  => $this->created_at->toIso8601String(),
            'dismissed_at'  => $this->dismissed_at ? $this->dismissed_at->toIso8601String() : null,
            
            'notification'  => [
                'id'           => $this->notification->id,
                'title'        => $this->notification->title,
                'body'         => $this->notification->body,
                'category'     => $this->notification->category,
                'type'         => $this->notification->type,
                'icon'         => $this->notification->icon_class,
                'icon_color'   => UserAppNotificastion::iconColorMap()[$this->notification->type] ?? 'primary',
                'action_url'   => $this->notification->action_url,
                'action_label' => $this->notification->action_label,
                'sender'       => $this->notification->creator ? $this->notification->creator->name : 'System Pipeline',
            ]
        ];
    }
}