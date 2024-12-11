<?php

namespace App\Http\Controllers;

use App\Http\Resources\AttributeResource;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Lang;

class AttributeController extends Controller implements HasMiddleware
{


    public static function middleware()
    {
        return ['auth:api'];
    }
    public function getAll(Request $request)
    {
        $attributes = Attribute::with('attributeValues')->get();
        return $this->ApiResponseFormatted(200,AttributeResource::collection($attributes), Lang::get('api.success'), $request);
    }


    public function create(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        try {

            $attribute = new Attribute();
            $attribute->setTranslations('name', [
                'en' => $validated['name_en'],
                'ar' => $validated['name_ar'],
            ]);
            $attribute->save();

            return $this->ApiResponseFormatted(201, AttributeResource::make($attribute), Lang::get('api.created'), $request);
        }catch (\Exception $e){
            return $this->ApiResponseFormatted(500, [], $e->getMessage(), $request);
        }
    }

    public function getOne(Request $request, $id)
    {
        $attribute = Attribute::with('attributeValues')->find($id);
        if (!$attribute) {
            return $this->ApiResponseFormatted(404, [], Lang::get('api.not_found'), $request);
        }
        return $this->ApiResponseFormatted(200, AttributeResource::make($attribute), Lang::get('api.success'), $request);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        try {
            $attribute = Attribute::find($id);
            if (!$attribute) {
                return $this->ApiResponseFormatted(404, [], Lang::get('api.not_found'), $request);
            }
            $attribute->setTranslations('name', [
                'en' => $validated['name_en'],
                'ar' => $validated['name_ar'],
            ]);
            $attribute->save();

            return $this->ApiResponseFormatted(200, AttributeResource::make($attribute), Lang::get('api.updated'), $request);
        }catch (\Exception $e){
            return $this->ApiResponseFormatted(500, [], $e->getMessage(), $request);
        }
    }

    public function delete(Request $request, $id)
    {
        $attribute = Attribute::find($id);
        if (!$attribute) {
            return $this->ApiResponseFormatted(404, [], Lang::get('api.not_found'), $request);
        }
        try {
            $attribute->delete();
            return $this->ApiResponseFormatted(200, [], Lang::get('api.deleted'), $request);
        }catch (\Exception $e){
            return $this->ApiResponseFormatted(500, [], $e->getMessage(), $request);
        }
    }



}
