<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Scopes\ActiveScope;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Routing\Controllers\HasMiddleware;

class CategoriesController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return ['auth:api'];
    }
    public function getAll(Request $request)
    {
        $categories = Category::query()->withoutGlobalScope(ActiveScope::class)->orderBy('order')->get();

        return $this->ApiResponseFormatted(200, CategoryResource::collection($categories), 'success', $request);
    }

    public function getCategory(Request $request, $id)
    {
        $category = Category::query()->withoutGlobalScope(ActiveScope::class)->find($id);
        if ($category == null) {
            return $this->ApiResponseFormatted(404, null, 'Category not found', $request);
        }

        return $this->ApiResponseFormatted(200, CategoryResource::make($category), 'success', $request);
    }


    public function create(Request $request)
    {
        $validator = validator($request->only('name_ar', 'name_en', 'slug', 'image', 'active', 'order', 'published', 'icon', 'color', 'parent_id'), [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'active' => 'boolean',
            'order' => 'numeric',
            'published' => 'boolean',
            'icon' => 'string|max:255',
            'color' => 'string|max:255',
            'parent_id' => 'numeric|nullable',
        ]);

        if ($validator->fails()) {
            return $this->ApiResponseFormatted(400, null, $validator->errors()->first(), $request);
        }

        try {
            $categoryName = [
                'ar' => $request->name_ar,
                'en' => $request->name_en,
            ];

            $category = Category::create([
                'name' => $categoryName,
                'slug' => $request->slug,
                'active' => $request->active ?? 1,
                'order' => $request->order ?? 0,
                'published' => $request->published ?? 1,
                'icon' => $request->icon,
                'color' => $request->color,
                'parent_id' => $request->parent_id,
            ]);


            if ($request->has('image')) {
                $path = $request->file('image')->store('images', 'public');
                $category->image = $path;
                $category->save();
            }

            return $this->ApiResponseFormatted(201, new CategoryResource($category), 'success', $request);
        } catch (\Exception $e) {
            return $this->ApiResponseFormatted(500, null, $e->getMessage(), $request);
        }

    }

    public function update(Request $request, $id)
    {
        $category = Category::query()->withoutGlobalScope(ActiveScope::class)->find($id);
        if ($category == null) {
            return $this->ApiResponseFormatted(404, null, 'Category not found', $request);
        }

        $validator = validator($request->only('name_ar', 'name_en', 'slug', 'image', 'active', 'order', 'published', 'parent_id'), [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'active' => 'boolean',
            'order' => 'numeric',
            'published' => 'boolean',
            'parent_id' => 'numeric|nullable',
        ]);

        if ($validator->fails()) {
            return $this->ApiResponseFormatted(400, null, $validator->errors()->first(), $request);
        }

        try {
            $categoryName = [
                'ar' => $request->name_ar,
                'en' => $request->name_en,
            ];

            $category->name = $categoryName;
            $category->slug = $request->slug;
            $category->active = $request->active ?? 1;
            $category->order = $request->order;
            $category->published = $request->published ?? 1;
            $category->icon = $request->icon;
            $category->color = $request->color;
            $category->parent_id = $request->parent_id;

            if ($request->has('image')) {
                $path = $request->file('image')->store('images', 'public');
                $category->image = $path;
            }

            if ($category->isDirty()) {
                $category->save();
            }

            return $this->ApiResponseFormatted(200, new CategoryResource($category), 'success', $request);
        } catch (\Exception $e) {
            return $this->ApiResponseFormatted(500, null, $e->getMessage(), $request);
        }
    }

    public function delete(Request $request, $id)
    {
        $category = Category::query()->withoutGlobalScope(ActiveScope::class)->find($id);
        if ($category == null) {
            return $this->ApiResponseFormatted(404, null, 'Category not found', $request);
        }

        try {
            $category->categories()->delete();


            $category->delete();
            return $this->ApiResponseFormatted(200, null, 'Category deleted successfully', $request);
        } catch (\Exception $e) {
            return $this->ApiResponseFormatted(500, null, $e->getMessage(), $request);
        }
    }


}
