<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductsController extends Controller
{
    public function getAll()
    {
        $products = Product::with(['images', 'categories'])->get();

        return response()->json(['data' => $products], 200);
    }

    public function create(Request $request)
    {
        $validator = validator($request->only(
            'name',
            'description',
            'purchase_price',
            'published',
            'slug',
            'approved',
            'brand_id',
            'cash_on_delivery',
            'free_shipping',
            'user_id',
            'stock',
            'discount_type',
            'discount',
            'discount_start',
            'discount_end',
            'external_link',
            'num_of_sales',
            'rating',
            'main_image',
            'images',
            'categories'
        ), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'purchase_price' => 'required|numeric',
            'published' => 'boolean',
            'slug' => 'required|string|max:255',
            'approved' => 'boolean',
            'brand_id' => 'numeric',
            'cash_on_delivery' => 'boolean',
            'free_shipping' => 'boolean',
            'user_id' => 'required|numeric',
            'stock' => 'required|numeric',
            'discount_type' => 'string|max:50',
            'discount' => 'numeric',
            'discount_start' => 'date',
            'discount_end' => 'date',
            'external_link' => 'string|max:255',
            'num_of_sales' => 'numeric',
            'rating' => 'numeric',
            'main_image' => 'string|max:255',
            'images' => 'array',
            'categories' => 'array'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $data = $request->only('name',
                'description',
                'purchase_price',
                'published',
                'slug',
                'approved',
                'brand_id',
                'cash_on_delivery',
                'free_shipping',
                'user_id',
                'stock',
                'discount_type',
                'discount',
                'discount_start',
                'discount_end',
                'external_link',
                'num_of_sales',
                'rating');

            $product = Product::create($data);

            if ($request->has('main_image')) {
                $product->images()->create([
                    'path' => $request->main_image,
                    'imageable_id' => $product->id,
                    'imageable_type' => Product::class,
                    'orignal' => true
                ]);
            }

            if ($request->has('images')) {
                foreach ($request->images as $image) {
                   $product->images()->create([
                        'path' => $image,
                        'imageable_id' => $product->id,
                        'imageable_type' => Product::class,
                        'orignal' => false
                    ]);
                }
            }

            if ($request->has('categories')) {
                $product->categories()->attach($request->categories);
            }


            return response()->json(['data' => $product], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
}
