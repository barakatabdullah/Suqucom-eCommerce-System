<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoriesController extends Controller
{
    public function getAll()
    {
        $categories = Category::all();

        return response()->json(['data' => $categories], 200);
    }

    public function create(Request $request)
    {
        $validator = validator($request->only('name', 'slug', 'image', 'active', 'order', 'published', 'icon', 'color', 'parent_id'), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'image' => 'string|max:255',
            'active' => 'boolean',
            'order' => 'numeric',
            'published' => 'boolean',
            'icon' => 'string|max:255',
            'color' => 'string|max:255',
            'parent_id' => 'numeric|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $data = $request->only('name', 'slug', 'image', 'active', 'order', 'published', 'icon', 'color', 'parent_id');

            $category = Category::create($data);

            return response()->json(['data' => $category], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
}
