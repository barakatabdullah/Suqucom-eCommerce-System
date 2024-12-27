<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Models\Scopes\ActiveScope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Lang;

class BrandController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return ['auth:admin'];
    }

    public function getBrands(Request $request)
    {
        $brands = Brand::withoutGlobalScope(ActiveScope::class)->get();
        return $this->ApiResponseFormatted(200, BrandResource::collection($brands), Lang::get('api.success'), $request);
    }

    public function getBrand(Request $request, $id)
    {
        $brand = Brand::withoutGlobalScope(ActiveScope::class)->find($id);
        if (!$brand) {
            return $this->ApiResponseFormatted(404, null, Lang::get('api.not_found'), $request);
        }
        return $this->ApiResponseFormatted(200, BrandResource::make($brand), Lang::get('api.success'), $request);
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            "name_ar" => "required|string|max:255",
            "name_en" => "required|string|max:255",
            "logo" => "image|mimes:jpeg,png,jpg,gif,svg|max:2048",
            "active" => "boolean",
            "meta_title_ar" => "string|max:255",
            "meta_title_en" => "string|max:255",
            "meta_description_ar" => "string|max:255",
            "meta_description_en" => "string|max:255",
            "slug" => "string|max:255",
        ]);

        try {
            $brand = new Brand();
            $brand->setTranslations('name', [
                'en' => $validated['name_en'],
                'ar' => $validated['name_ar'],
            ]);
            $brand->setTranslations('meta_title', [
                'en' => $validated['meta_title_en'],
                'ar' => $validated['meta_title_ar'],
            ]);

            $brand->setTranslations('meta_description', [
                'en' => $validated['meta_description_en'],
                'ar' => $validated['meta_description_ar'],
            ]);
            $brand->slug = $validated['slug'];
            $brand->active = $validated['active'] ?? 1;

            if ($request->hasFile('logo')) {
                $brand->addMediaFromRequest('logo')->toMediaCollection('brands');
            }

            $brand->save();

            return $this->ApiResponseFormatted(201, BrandResource::make($brand), Lang::get('api.created'), $request);
        } catch (\Exception $e) {
            return $this->ApiResponseFormatted(500, null, $e->getMessage(), $request);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            "name_ar" => "required|string|max:255",
            "name_en" => "required|string|max:255",
            "logo" => "image|mimes:jpeg,png,jpg,gif,svg|max:2048",
            "active" => "boolean",
            "meta_title_ar" => "string|max:255",
            "meta_title_en" => "string|max:255",
            "meta_description_ar" => "string|max:255",
            "meta_description_en" => "string|max:255",
            "slug" => "string|max:255",
        ]);

        $brand = Brand::find($id);
        if (!$brand) {
            return $this->ApiResponseFormatted(404, null, Lang::get('api.not_found'), $request);
        }

        try {
            $brand->setTranslations('name', [
                'en' => $validated['name_en'],
                'ar' => $validated['name_ar'],
            ]);
            $brand->setTranslations('meta_title', [
                'en' => $validated['meta_title_en'],
                'ar' => $validated['meta_title_ar'],
            ]);

            $brand->setTranslations('meta_description', [
                'en' => $validated['meta_description_en'],
                'ar' => $validated['meta_description_ar'],
            ]);
            $brand->slug = $validated['slug'];
            $brand->active = $validated['active'] ?? $brand->active;

            if ($request->hasFile('logo')) {
                $brand->clearMediaCollection('brands');
                $brand->addMediaFromRequest('logo')->toMediaCollection('brands');
            }

            $brand->save();

            return $this->ApiResponseFormatted(200, BrandResource::make($brand), Lang::get('api.updated'), $request);
        } catch (\Exception $e) {
            return $this->ApiResponseFormatted(500, null, $e->getMessage(), $request);
        }
    }

    public function delete(Request $request, $id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return $this->ApiResponseFormatted(404, null, Lang::get('api.not_found'), $request);
        }
        $brand->delete();
        return $this->ApiResponseFormatted(200, null, Lang::get('api.deleted'), $request);
    }



}
