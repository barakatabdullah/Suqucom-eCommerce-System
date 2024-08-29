<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsController extends Controller
{
    public function getAllPermissions()
    {
        $permissions = Permission::all();

        return response()->json(['data' => $permissions], 200);
    }

    public function getAllRoles()
    {
        $roles=Role::with('permissions')->get();

        return response()->json(['data' => $roles], 200);
    }

    public function assignPermissionsToRole(Request $request, $role_id)
    {
        $role = Role::find($role_id);
        $permissions = Permission::whereIn('id', $request->permission_ids)->get();

        $role->syncPermissions($permissions);

        return response()->json(['data' => 'Permissions assigned to role'], 200);

    }

}
