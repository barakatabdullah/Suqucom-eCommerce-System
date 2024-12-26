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
//            "phone"=>$this->phone,
            'email_verified' => !!$this->email_verified_at,


        ];
    }
}
