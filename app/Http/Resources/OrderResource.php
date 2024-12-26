<?php

namespace App\Http\Resources;

use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            "user"=>UserResource::make($this->user),
          "order_details"=>OrderDetailsResource::collection($this->orderDetails),
            "shipping_address"=>$this->shipping_address,
            "additional_info"=>$this->additional_info,
            "delivery_status"=>$this->delivery_status,
            "payment_type"=>$this->payment_type,
            "payment_status"=>$this->payment_status,
            "grand_total"=>$this->grand_total,
            "discount"=>$this->discount,

        ];
    }
}
