<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderDetailsResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Lang;

class OrderController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return ['auth:api'];
    }

    public function getAll(Request $request)
    {
        $orders = Order::all();
        return $this->ApiResponseFormatted(200, OrderResource::collection($orders), Lang::get('api.success'), $request);
    }

    public function getOrder(Request $request, $id)
    {
        $order = Order::find($id);
        if ($order == null) {
            return $this->ApiResponseFormatted(404, null, Lang::get('api.order_not_found'), $request);
        }
        return $this->ApiResponseFormatted(200, OrderResource::make($order), Lang::get('api.success'), $request);
    }

    public function create(Request $request)
    {

        $validated = $request->validate([
            "products" => "required|array",
            "products.*.id" => "required|integer",
            "products.*.quantity" => "required|integer",
            "products.*.price" => "required|numeric",
            'shipping_address' => 'required|string',
            'additional_info' => 'string',
            'payment_type' => 'required|string',
            'payment_status' => 'required|string',
            'discount' => 'required|numeric',
        ]);

        try {
            $order = new Order();
            $order->user_id = auth()->user()->id;
            $order->shipping_address = $validated['shipping_address'];
            $order->additional_info = $validated['additional_info'];
            $order->payment_type = $validated['payment_type'];
            $order->payment_status = $validated['payment_status'];
            $order->discount = $validated['discount'];
            $order->save();

            foreach ($validated['products'] as $product) {
                $order->orderDetails()->create([
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                ]);
            }

            return $this->ApiResponseFormatted(201, OrderResource::make($order), Lang::get('api.order_created'), $request);
        } catch (\Exception $e) {
            return $this->ApiResponseFormatted(500, null, $e->getMessage(), $request);
        }


    }

    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        if ($order == null) {
            return $this->ApiResponseFormatted(404, null, Lang::get('api.order_not_found'), $request);
        }

        $validated = $request->validate([
            "products" => "required|array",
            "products.*.id" => "required|integer",
            "products.*.quantity" => "required|integer",
            "products.*.price" => "required|numeric",
            'shipping_address' => 'required|string',
            'additional_info' => 'string',
            'payment_type' => 'required|string',
            'payment_status' => 'required|string',
            'discount' => 'required|numeric',
        ]);

        try {
            $order->shipping_address = $validated['shipping_address'];
            $order->additional_info = $validated['additional_info'];
            $order->payment_type = $validated['payment_type'];
            $order->payment_status = $validated['payment_status'];
            $order->discount = $validated['discount'];
            $order->save();

            $order->orderDetails()->delete();

            foreach ($validated['products'] as $product) {
                $order->orderDetails()->create([
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                ]);
            }

            return $this->ApiResponseFormatted(200, OrderResource::make($order), Lang::get('api.order_updated'), $request);
        } catch (\Exception $e) {
            return $this->ApiResponseFormatted(500, null, $e->getMessage(), $request);
        }
    }

    public function delete(Request $request, $id)
    {
        $order = Order::find($id);
        if ($order == null) {
            return $this->ApiResponseFormatted(404, null, Lang::get('api.order_not_found'), $request);
        }

        try {
            $order->orderDetails()->delete();
            $order->delete();
            return $this->ApiResponseFormatted(200, null, Lang::get('api.order_deleted'), $request);
        } catch (\Exception $e) {
            return $this->ApiResponseFormatted(500, null, $e->getMessage(), $request);
        }
    }



}
