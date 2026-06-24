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
        // $this directly references a single record row instance from the UserAppNotificastion table
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'body'          => $this->body,
            'category'      => $this->category,
            'type'          => $this->type,
            'status'        => $this->status,
            'icon'          => $this->icon_class ?? $this->icon,
            'icon_color'    => UserAppNotificastion::iconColorMap()[$this->type] ?? 'primary',
            'action_url'    => $this->action_url,
            'action_label'  => $this->action_label,
            'is_broadcast'  => (bool) $this->is_broadcast,
            'scheduled_at'  => $this->scheduled_at ? $this->scheduled_at->toIso8601String() : null,
            'sent_at'       => $this->sent_at ? $this->sent_at->toIso8601String() : null,
            'created_at'    => $this->created_at->toIso8601String(),
            'sender'        => $this->creator ? $this->creator->name : 'System Pipeline',
            
            // Aggregated values safely pulled from model getters/appends
            'metrics'       => [
                'read_count'        => $this->read_count ?? 0,
                'total_recipients'  => $this->total_recipients_count ?? 0,
            ]
        ];
    }
}