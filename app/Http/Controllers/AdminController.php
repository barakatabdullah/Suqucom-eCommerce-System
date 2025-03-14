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
        $request->validate([
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'sort_by' => 'nullable|string|in:id,name,email,created_at,updated_at',
            'sort_direction' => 'nullable|string|in:asc,desc',
            'search' => 'string|max:100|nullable',
            'role' => 'nullable|string',
            'active' => 'nullable|boolean',
        ]);

        $query = Admin::query()->with(['roles', 'media']);

        // Filtering
        if ($request->has('active')) {
            $query->where('active', $request->active);
        }

        if ($request->has('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Search functionality
        if ($request->has('search')) {
            $searchTerm = "%{$request->search}%";
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm);
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 2);
        $admins = $query->paginate($perPage);

        return $this->ApiResponseFormatted(
            200,
            [
                'data' => AdminResource::collection($admins),
                'meta' => [
                    'current_page' => $admins->currentPage(),
                    'last_page' => $admins->lastPage(),
                    'per_page' => $admins->perPage(),
                    'total' => $admins->total(),
                    'filters' => $request->only(['search', 'role', 'active', 'sort_by', 'sort_direction']),
                ]
            ],
            Lang::get('api.success'),
            $request
        );
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
        $validated = $request->validate([
            'locale' => 'required|string|in:en,ar',
        ]);

        $model = $request->user();
        $model->locale = $validated['locale'];
        $model->save();

        return $this->ApiResponseFormatted(200, null, Lang::get('api.updated'), $request);
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
