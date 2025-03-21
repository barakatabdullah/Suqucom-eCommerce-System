<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Lang;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return ['auth:admin'];
    }

    public function getAllPermissions(Request $request)
    {
        $permissions = Permission::all();
        $groupedPermissions = $permissions->groupBy('section');

        $result = [];
        foreach ($groupedPermissions as $section => $perms) {
            $sectionName = Lang::get('permissions.' . $section);
            $result[$sectionName] = PermissionResource::collection($perms);
        }

        return $this->ApiResponseFormatted(200, $result, Lang::get('api.success'), $request);
    }

    public function getAllRoles(Request $request)
    {
        $roles = Role::with('permissions')->get();

        return $this->ApiResponseFormatted(200, RoleResource::collection($roles), Lang::get('api.success'), $request);
    }

    public function getRole(Request $request,$role_id)
    {
        $role = Role::with('permissions')->find($role_id);

        return $this->ApiResponseFormatted(200, RoleResource::make($role), Lang::get('api.success'), $request);
    }

    public function updateRoleWithPermissions(Request $request, $role_id)
    {
        $validator = validator($request->only('name', 'guard_name', 'permission_ids'), [
            'name' => 'required|string|max:255',
            'guard_name' => 'required|string|max:255',
            'permission_ids' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $role = Role::find($role_id);

            $role->update([
                'name' => $request->name,
                'guard_name' => $request->guard_name,
            ]);


            $permissions = Permission::whereIn('id', $request->permission_ids)->get();

            $role->syncPermissions($permissions);

            return response()->json(['data' => 'Permissions assigned to role'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Role not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }


    }


    public function createRoleWithPermissions(Request $request)
    {
        $validator = validator($request->only('name', 'guard_name', 'permission_ids'), [
            'name' => 'required|string|max:255',
            'guard_name' => 'required|string|max:255',
            'permission_ids' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => $request->guard_name,
            ]);

            $permissions = Permission::whereIn('id', $request->permission_ids)->get();

            $role->syncPermissions($permissions);

            return response()->json(['data' => $role], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function deleteRole($role_id)
    {
//        if(Auth::user()->cannot('delete roles')){
//            return response()->json( 'You do not have permission to delete roles.', 403);
//        }
        $role = Role::find($role_id);

        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        $role->delete();

        return response()->json(['data' => 'Role deleted'], 200);

    }

}
