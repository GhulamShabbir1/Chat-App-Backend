<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'workspace_id' => (string) $this->workspace_id,
            'owner_id' => (string) $this->owner_id,
            'user_ids' => $this->user_ids,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
