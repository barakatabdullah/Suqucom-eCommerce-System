<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
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
            "name_ar" => $this->getTranslation("name", "ar"),
            "name_en" => $this->getTranslation("name", "en"),
             "active"=>$this->active,
            "meta_title" => $this->meta_title,
            "meta_title_ar" => $this->getTranslation("meta_title", "ar"),
            "meta_title_en" => $this->getTranslation("meta_title", "en"),
            "meta_description" => $this->meta_description,
            "meta_description_ar" => $this->getTranslation("meta_description", "ar"),
            "meta_description_en" => $this->getTranslation("meta_description", "en"),
            "slug" => $this->slug,
            "logo" => $this->getFirstMedia('brands') ? mediaAppUrl($this->getFirstMedia('brands')->getUrl()) : null,
        ];
    }
}
