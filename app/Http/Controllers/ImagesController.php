<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImagesController extends Controller
{

    public function upload(Request $request)
    {

        $validator = validator($request->only('image'), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }


        try {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            return response()->json(['data' => ['image' => $imageName]], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'File upload failed'], 500);
        }
    }
}
