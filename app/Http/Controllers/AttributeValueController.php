<?php

namespace App\Http\Controllers;

use App\Http\Resources\AttributeValueResource;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Lang;

class AttributeValueController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return ['auth:admin'];
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'attribute_id' => 'required|integer',
            'value' => 'required|string|max:255',
        ]);

        try {
            $attributeValue = new AttributeValue();

            $attribute = Attribute::find($validated['attribute_id']);

            if (!$attribute) {
                return $this->ApiResponseFormatted(404, [], Lang::get('api.not_found'), $request);
            }

            $attributeValue->attribute_id = $attribute->id;
            $attributeValue->value = $validated['value'];
            $attributeValue->save();


            return $this->ApiResponseFormatted(201, AttributeValueResource::make($attributeValue), Lang::get('api.created'), $request);
        }catch (\Exception $e){
            return $this->ApiResponseFormatted(500, [], $e->getMessage(), $request);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255',
        ]);

        $attributeValue = AttributeValue::find($id);

        if (!$attributeValue) {
            return $this->ApiResponseFormatted(404, [], Lang::get('api.not_found'), $request);
        }

        try {
            if ($attributeValue->value !== $validated['value']) {
                $attributeValue->value = $validated['value'];
                $attributeValue->save();
            }

            return $this->ApiResponseFormatted(200, AttributeValueResource::make($attributeValue), Lang::get('api.updated'), $request);
        }catch (\Exception $e){
            return $this->ApiResponseFormatted(500, null, $e->getMessage(), $request);
        }


    }

    public function delete(Request $request,$id)
    {
        $attributeValue = AttributeValue::find($id);

        if (!$attributeValue) {
            return $this->ApiResponseFormatted(404, null, Lang::get('api.not_found'), $request);
        }

        try {
            $attributeValue->delete();
            return $this->ApiResponseFormatted(200, null, Lang::get('api.deleted'), $request);
        }catch (\Exception $e){
            return $this->ApiResponseFormatted(500, null, $e->getMessage(), $request);
        }
    }



}
