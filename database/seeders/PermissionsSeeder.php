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

        // Create permissions
        $permissions = [
            'manage products',
            'create products',
            'edit products',
            'delete products',
            'view products',
            'manage orders',
            'create orders',
            'edit orders',
            'delete orders',
            'view orders',
            'manage categories',
            'create categories',
            'edit categories',
            'delete categories',
            'view categories',
            'manage users',
            'create users',
            'edit users',
            'delete users',
            'view users',
            'manage settings',
            'view dashboard',
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
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        Role::create(['name' => 'Super-Admin']);


    }
}
