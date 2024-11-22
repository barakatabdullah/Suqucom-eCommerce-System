<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoriesController extends Controller
{
    public function getAll(Request $request)
    {
        $categories = Category::all();

        return $this->ApiResponseFormatted(200,CategoryResource::collection($categories),'success',$request);
    }

    public function create(Request $request)
    {
        $validator = validator($request->only('name', 'slug', 'image', 'active', 'order', 'published', 'icon', 'color', 'parent_id'), [
            'name' => 'required|string|max:255',
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
            return $this->ApiResponseFormatted(400,null,$validator->errors()->first(),$request);
        }

        try {
            $data = $request->only('name', 'slug', 'active', 'order', 'published', 'parent_id');

            $category = Category::create($data);

            if($request->has('image')){
                $path = $request->file('image')->store('images', 'public');
                $category->image = $path;
                $category->save();
            }

            return $this->ApiResponseFormatted(201,new CategoryResource($category),'success',$request);
        } catch (\Exception $e) {
            return $this->ApiResponseFormatted(500,null,$e->getMessage(),$request);
        }

    }
}
