<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_ar'=> $this->getTranslation("name", "ar"),
            'name_en'=> $this->getTranslation("name", "en"),
            'slug' => $this->slug,
            'image' => appUrl($this->image),
            'active' => $this->active,
            'order' => $this->order,
            'published' => $this->published,
            'parent_id' => $this->parent_id,
            'parent_category'=> CategoryResource::make($this->parentCategory),
        ];
    }
}
