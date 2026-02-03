<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            '_id' => (string) $this->_id,
            'content' => $this->content,
            'type' => $this->type,
            'sender_id' => (string) $this->sender_id,
            'channel_id' => $this->channel_id ? (string) $this->channel_id : null,
            'receiver_id' => $this->receiver_id ? (string) $this->receiver_id : null,
            'file_attachment_id' => $this->file_attachment_id ? (string) $this->file_attachment_id : null,
            'workspace_id' => (string) $this->workspace_id,
            'team_id' => $this->team_id ? (string) $this->team_id : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
