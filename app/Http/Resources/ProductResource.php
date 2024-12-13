<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'=>$this->name,
            'name_ar'=>$this->getTranslation("name", "ar"),
            'name_en'=>$this->getTranslation("name", "en"),
            'description'=>$this->description,
            'description_ar'=>$this->getTranslation("description", "ar"),
            'description_en'=>$this->getTranslation("description", "en"),
            'purchase_price'=>$this->purchase_price,
            'published'=>$this->published,
            'slug'=>$this->slug,
            'approved'=>$this->approved,
            'brand'=>BrandResource::make($this->brand),
            'cash_on_delivery'=>$this->cash_on_delivery,
            'free_shipping'=>$this->free_shipping,
            'user'=>$this->user,
            'stock'=>$this->stock,
            'discount_type'=>$this->discount_type,
            'discount'=>$this->discount,
            'discount_start'=>$this->discount_start,
            'discount_end'=>$this->discount_end,
            'external_link'=>$this->external_link,
            'num_of_sales'=>$this->num_of_sales,
            'rating'=>$this->rating,
            'main_image'=>$this->getFirstMedia('products.main') ? mediaAppUrl($this->getFirstMedia('products.main')->getUrl()) : null,
            'images'=>$this->getMedia('products'),
            'categories'=>CategoryResource::collection($this->categories),
        ];
    }
}
