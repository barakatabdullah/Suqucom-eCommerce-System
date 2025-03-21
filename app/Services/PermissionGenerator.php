<?php

namespace App\Services;

use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class PermissionGenerator
{
    public function generateForModel($model, $guardName = 'admin')
    {
        // Get pluralized model name in lowercase
        $modelName = Str::plural(Str::kebab(class_basename($model)));

        // Standard CRUD operations
        $operations = ['view', 'list', 'create', 'edit', 'delete','restore','force-delete'];
        $permissions = [];

        foreach ($operations as $operation) {
            $permissionName = "{$operation}-{$modelName}";
            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => $guardName,
                'section'=> $modelName,
                'action' => $operation
            ]);
            $permissions[] = $permission;
        }

        return $permissions;
    }
}
