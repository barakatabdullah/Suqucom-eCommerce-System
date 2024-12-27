<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Routing\Controllers\HasMiddleware;
use Lang;

class ProductsController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return ['auth:admin'];
    }
    public function getProducts(Request $request)
    {
        $products = Product::all();

        return $this->ApiResponseFormatted(200, ProductResource::collection($products), Lang::get('api.success'), $request);
    }

    public function getProduct(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->ApiResponseFormatted(404, null, Lang::get('api.not_found'), $request);
        }
        return $this->ApiResponseFormatted(200, ProductResource::make($product), Lang::get('api.success'), $request);
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'purchase_price' => 'required|numeric',
            'published' => 'boolean',
            'slug' => 'required|string|max:255',
            'approved' => 'boolean',
            'brand_id' => 'numeric',
            'cash_on_delivery' => 'boolean',
            'free_shipping' => 'boolean',
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

        try {
            $product = new Product();
            $product->setTranslations('name', [
                'en' => $validated['name_en'],
                'ar' => $validated['name_ar'],
            ]);
            $product->setTranslations('description', [
                'en' => $validated['description_en'],
                'ar' => $validated['description_ar'],
            ]);
            $product->purchase_price = $validated['purchase_price'];
            $product->published = $validated['published'];
            $product->slug = $validated['slug'];
            $product->approved = $validated['approved'];
            $product->brand_id = $validated['brand_id'];
            $product->cash_on_delivery = $validated['cash_on_delivery'];
            $product->free_shipping = $validated['free_shipping'];
            $product->user_id = auth()->id();
            $product->stock = $validated['stock'];
            $product->discount_type = $validated['discount_type'];
            $product->discount = $validated['discount'];
            $product->discount_start = $validated['discount_start'];
            $product->discount_end = $validated['discount_end'];
            $product->external_link = $validated['external_link'];
            $product->num_of_sales = $validated['num_of_sales'];
            $product->rating = $validated['rating'];
            $product->save();

            if ($request->has('main_image')) {
                $product->addMediaFromRequest('main_image')->toMediaCollection('products.main');
            }

            if ($request->has('images')) {
                foreach ($request->images as $image) {
                    $product->addMediaFromRequest($image)->toMediaCollection('products');
                }
            }

            if ($request->has('categories')) {
                $product->categories()->attach($request->categories);
            }

            return $this->ApiResponseFormatted(201, ProductResource::make($product), Lang::get('api.created'), $request);
        } catch (\Exception $e) {
            return $this->ApiResponseFormatted(500, null, $e->getMessage(), $request);

        }
    }

    public function update(Request $request,$id)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'purchase_price' => 'required|numeric',
            'published' => 'boolean',
            'slug' => 'required|string|max:255',
            'approved' => 'boolean',
            'brand_id' => 'numeric',
            'cash_on_delivery' => 'boolean',
            'free_shipping' => 'boolean',
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

        $product = Product::find($id);
        if (!$product) {
            return $this->ApiResponseFormatted(404, null, Lang::get('api.not_found'), $request);
        }

        try {
            $product->setTranslations('name', [
                'en' => $validated['name_en'],
                'ar' => $validated['name_ar'],
            ]);
            $product->setTranslations('description', [
                'en' => $validated['description_en'],
                'ar' => $validated['description_ar'],
            ]);
            $product->purchase_price = $validated['purchase_price'];
            $product->published = $validated['published'];
            $product->slug = $validated['slug'];
            $product->approved = $validated['approved'];
            $product->brand_id = $validated['brand_id'];
            $product->cash_on_delivery = $validated['cash_on_delivery'];
            $product->free_shipping = $validated['free_shipping'];
            $product->stock = $validated['stock'];
            $product->discount_type = $validated['discount_type'];
            $product->discount = $validated['discount'];
            $product->discount_start = $validated['discount_start'];
            $product->discount_end = $validated['discount_end'];
            $product->external_link = $validated['external_link'];
            $product->num_of_sales = $validated['num_of_sales'];
            $product->rating = $validated['rating'];
            $product->save();

            if ($request->has('main_image')) {
                $product->getFirstMedia('products.main')->delete();
                $product->addMediaFromRequest('main_image')->toMediaCollection('products.main');
            }

            if ($request->has('images')) {
                foreach ($request->images as $image) {
                    $product->addMediaFromRequest($image)->toMediaCollection('products');
                }
            }

            if ($request->has('categories')) {
                $product->categories()->sync($request->categories);
            }

            return $this->ApiResponseFormatted(200, ProductResource::make($product), Lang::get('api.updated'), $request);
        } catch (\Exception $e) {
            return $this->ApiResponseFormatted(500, null, $e->getMessage(), $request);
        }

    }

    public function delete(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->ApiResponseFormatted(404, null, Lang::get('api.not_found'), $request);
        }
        $product->delete();
        return $this->ApiResponseFormatted(200, null, Lang::get('api.deleted'), $request);
    }



}
