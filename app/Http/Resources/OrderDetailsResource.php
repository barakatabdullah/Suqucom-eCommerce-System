<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "product"=>ProductResource::collection($this->product->get()),
            "quantity"=>$this->quantity,
            "price"=>$this->price,
//            "order"=>OrderResource::make($this->order),

        ];
    }
}
