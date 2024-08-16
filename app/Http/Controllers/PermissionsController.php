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
        $roles=Role::all();

        return response()->json(['data' => $roles], 200);
    }


}
