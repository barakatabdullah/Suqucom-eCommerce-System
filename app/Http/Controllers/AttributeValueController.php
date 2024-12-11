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
        return ['auth:api'];
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

}
