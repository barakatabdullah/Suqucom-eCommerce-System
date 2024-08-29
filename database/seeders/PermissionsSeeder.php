<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define categorized permissions
        $permissions = [
            'roles' => [
                'manage roles',
                'create roles',
                'edit roles',
                'delete roles',
                'view roles',
            ],
            'products' => [
                'manage products',
                'create products',
                'edit products',
                'delete products',
                'view products',
            ],
            'orders' => [
                'manage orders',
                'create orders',
                'edit orders',
                'delete orders',
                'view orders',
            ],
            'categories' => [
                'manage categories',
                'create categories',
                'edit categories',
                'delete categories',
                'view categories',
            ],
            'users' => [
                'manage users',
                'create users',
                'edit users',
                'delete users',
                'view users',
            ],
            'settings' => [
                'manage settings',
                'view dashboard',
            ],
            'affiliates' => [
                'manage affiliates',
                'create affiliates',
                'edit affiliates',
                'delete affiliates',
                'view affiliates',
                'manage affiliate commissions',
                'create affiliate commissions',
                'edit affiliate commissions',
                'delete affiliate commissions',
                'view affiliate commissions',
            ],

        ];

        // Create permissions
        foreach ($permissions as $category => $perms) {
            foreach ($perms as $permission) {
                if (!Permission::where('name', $permission)->exists()) {
                    Permission::create([
                        'name' => $permission,
                        'guard_name' => 'api'
                    ]);
                }
            }
        }




    }
}
