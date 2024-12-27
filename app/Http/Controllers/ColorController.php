<?php

namespace App\Http\Controllers;

use App\Http\Resources\ColorResource;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Lang;

class ColorController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return ['auth:admin'];
    }

    public function getColors(Request $request)
    {
        $colors = Color::all();
        return $this->ApiResponseFormatted(200, ColorResource::collection($colors), Lang::get('api.success'), $request);
    }

    public function getColor(Request $request, $id)
    {
        $color = Color::find($id);
        if (!$color) {
            return $this->ApiResponseFormatted(404, null, Lang::get('api.not_found'), $request);
        }
        return $this->ApiResponseFormatted(200, ColorResource::make($color), Lang::get('api.success'), $request);
    }


    public function create(Request $request)
    {
        $validated = $request->validate([
            "name_ar" => "required|string|max:255",
            "name_en" => "required|string|max:255",
            "code" => "required|string|max:255",
        ]);

        try {
            $color = new Color();
            $color->setTranslations('name', [
                'en' => $validated['name_en'],
                'ar' => $validated['name_ar'],
            ]);
            $color->code = $validated['code'];
            $color->save();

            return $this->ApiResponseFormatted(201, ColorResource::make($color), Lang::get('api.created'), $request);
        } catch (\Exception $e) {
            return $this->ApiResponseFormatted(500, null, $e->getMessage(), $request);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            "name_ar" => "required|string|max:255",
            "name_en" => "required|string|max:255",
            "code" => "required|string|max:255",
        ]);

        $color = Color::find($id);
        if (!$color) {
            return $this->ApiResponseFormatted(404, null, Lang::get('api.not_found'), $request);
        }

        try {
            $color->setTranslations('name', [
                'en' => $validated['name_en'],
                'ar' => $validated['name_ar'],
            ]);
            $color->code = $validated['code'];
            $color->save();

            return $this->ApiResponseFormatted(200, ColorResource::make($color), Lang::get('api.updated'), $request);
        } catch (\Exception $e) {
            return $this->ApiResponseFormatted(500, null, $e->getMessage(), $request);
        }
    }

    public function delete(Request $request, $id)
    {
        $color = Color::find($id);
        if (!$color) {
            return $this->ApiResponseFormatted(404, null, Lang::get('api.not_found'), $request);
        }
        $color->delete();
        return $this->ApiResponseFormatted(200, null, Lang::get('api.deleted'), $request);
    }



}
