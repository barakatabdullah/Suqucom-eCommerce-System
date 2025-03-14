<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminResource;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Lang;
use Spatie\Permission\Models\Role;

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
            'active' => 'nullable|in:true,false,0,1',
        ]);

        $query = Admin::query()->with(['roles', 'media']);

        // Filtering
        if ($request->has('active')) {
            $query->where('active', filter_var($request->active, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->has('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Search functionality
        if ($request->has('search')) {
            $searchTerm = "%{$request->search}%";
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm);
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 10);
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
        try {
            // Retrieve admin with essential relationships
            $admin = Admin::with(['roles:id,name', 'media' => function ($query) {
                $query->where('collection_name', 'avatars');
            }])->findOrFail($id);

            return $this->ApiResponseFormatted(
                200,
                new AdminResource($admin),
                Lang::get('api.success'),
                $request
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->ApiResponseFormatted(
                404,
                null,
                Lang::get('api.not_found'),
                $request
            );
        } catch (\Exception $e) {
            return $this->ApiResponseFormatted(
                500,
                null,
                Lang::get('api.error'),
                $request
            );
        }
    }

public function create(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:admins,email',
        'password' => 'required|string|min:8',
        'active' => 'nullable|in:true,false,0,1',
        'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'roles' => 'nullable|array',
        'roles.*' => 'integer|exists:roles,id',
    ]);

    // Create admin with validated data
    $createData = [
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => bcrypt($validated['password']),
        'active' => isset($validated['active']) ? filter_var($validated['active'], FILTER_VALIDATE_BOOLEAN) : true,
    ];

    $admin = Admin::create($createData);

    // Handle roles assignment
    if ($request->has('roles')) {
        $roles = Role::whereIn('id', $validated['roles'])->get();
        $admin->syncRoles($roles);
    }

    // Handle avatar upload
    if ($request->hasFile('avatar')) {
        $admin->addMediaFromRequest('avatar')->toMediaCollection('avatars');
    }

    return $this->ApiResponseFormatted(201, new AdminResource($admin), Lang::get('api.created'), $request);
}

    public function update(Request $request, $id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return $this->ApiResponseFormatted(404, null, Lang::get('api.not_found'), $request);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $id,
            'password' => 'nullable|string|min:8',
            'active' => 'nullable|in:true,false,0,1',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'roles' => 'nullable|array',
            'roles.*' => 'integer|exists:roles,id',
            'remove_avatar' => 'nullable|in:0,1',
        ]);

        // Update admin with validated data
        $updateData = array_filter([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'active' => isset($validated['active']) ? filter_var($validated['active'], FILTER_VALIDATE_BOOLEAN) : $admin->active,
        ]);

        // Update password only if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = bcrypt($validated['password']);
        }

        $admin->update($updateData);

        // Handle roles assignment
        if ($request->has('roles')) {
            $roles = Role::whereIn('id', $validated['roles'])->get();
            $admin->syncRoles($roles);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $admin->clearMediaCollection('avatars');
            $admin->addMediaFromRequest('avatar')->toMediaCollection('avatars');
        } // Handle avatar removal
        elseif ($request->input('remove_avatar') == '1' && $admin->hasMedia('avatars')) {
            $admin->clearMediaCollection('avatars');
        }

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
