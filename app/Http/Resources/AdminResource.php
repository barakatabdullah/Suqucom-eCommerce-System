<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "is_email_verified" => !!$this->email_verified_at,
            "locale" => $this->locale,
            "active" => !!$this->active,
            "avatar" => $this->getFirstMediaUrl('avatars'),
            "roles" => RoleResource::collection($this->whenLoaded('roles')),
            "created_at" => $this->created_at,
        ];
    }
}
