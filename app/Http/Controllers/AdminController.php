<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminResource;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Lang;

class AdminController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return ['auth:admin'];
    }

    public function getCurrentAdmin(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        return $this->ApiResponseFormatted(200, AdminResource::make($admin), Lang::get('api.success'), $request);
    }


    public function getAll(Request $request)
    {
        $admins = Admin::with('roles', 'media')->get();
        return $this->ApiResponseFormatted(200, AdminResource::collection($admins), Lang::get('api.success'), $request);
    }

    public function getOne(Request $request, $id)
    {
        $admin = Admin::with('roles', 'media')->find($id);
        if (!$admin) {
            return $this->ApiResponseFormatted(404, null, Lang::get('api.not_found'), $request);
        }
        return $this->ApiResponseFormatted(200, new AdminResource($admin), Lang::get('api.success'), $request);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string',
        ]);
        $admin = Admin::create($request->all());
        return $this->ApiResponseFormatted(201, new AdminResource($admin), Lang::get('api.created'), $request);
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return $this->ApiResponseFormatted(404, null, Lang::get('api.not_found'), $request);
        }
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:admins,email,' . $id,
            'password' => 'required|string',
            'active' => 'required|boolean',
        ]);
        $admin->update($request->all());
        return $this->ApiResponseFormatted(200, new AdminResource($admin), Lang::get('api.updated'), $request);
    }

    public function setLocale(Request $request)
    {
        $validated=$request->validate([
            'locale' => 'required|string|in:en,ar',
        ]);

        $model = $request->user();
        $model->locale = $validated['locale'];
        $model->save();

        return $this->ApiResponseFormatted(200,null, Lang::get('api.updated'), $request);
    }

    public function delete(Request $request, $id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return $this->ApiResponseFormatted(404, null, Lang::get('api.not_found'), $request);
        }
        $admin->delete();
        return $this->ApiResponseFormatted(204, null, Lang::get('api.deleted'), $request);
    }
}
