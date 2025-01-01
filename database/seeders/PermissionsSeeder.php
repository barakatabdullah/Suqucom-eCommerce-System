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
            'admin'=> [
                'create_admin',
                'edit_admin',
                'delete_admin',
                'view_admin',
            ],
            'roles' => [
                'create_role',
                'edit_role',
                'delete_role',
                'view_role',
            ],
            'products' => [
                'create_product',
                'edit_product',
                'delete_product',
                'view_product',
            ],
            'attributes' => [
                'create_attribute',
                'edit_attribute',
                'delete_attribute',
                'view_attribute',
            ],
            'brands' => [
                'create_brand',
                'edit_brand',
                'delete_brand',
                'view_brand',
            ],
            'orders' => [
                'create_order',
                'edit_order',
                'delete_order',
                'view_order',
            ],
            'categories' => [
                'create_category',
                'edit_category',
                'delete_category',
                'view_category',
            ],
            'users' => [
                'create_user',
                'edit_user',
                'delete_user',
                'view_user',
            ],
            'ratings' => [
               'manage_ratings',
            ],
            'settings' => [
                'manage_settings',
            ],
//            'affiliates' => [
//                'manage affiliates',
//                'create affiliates',
//                'edit affiliates',
//                'delete affiliates',
//                'view affiliates',
//                'manage affiliate commissions',
//                'create affiliate commissions',
//                'edit affiliate commissions',
//                'delete affiliate commissions',
//                'view affiliate commissions',
//            ],

        ];

        // Create permissions
        foreach ($permissions as $category => $perms) {
            foreach ($perms as $permission) {
                if (!Permission::where('name', $permission)->exists()) {
                    Permission::create([
                        'name' => $permission,
                        'guard_name' => 'admin'
                    ]);
                }
            }
        }




    }
}
