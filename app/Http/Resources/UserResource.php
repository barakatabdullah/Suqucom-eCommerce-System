<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            "fname" => $this->fname,
            "lname" => $this->lname,
            "email" => $this->email,
            "locale" => $this->locale,
            "avatar" => $this->getFirstMediaUrl('avatars'),
            "created_at" => $this->created_at,
        ];
    }
}
