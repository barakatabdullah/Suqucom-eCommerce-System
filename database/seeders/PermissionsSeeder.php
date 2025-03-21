<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use App\Services\PermissionGenerator;
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

        $permissionGenerator = new PermissionGenerator();


// Generate permissions for all models
        $modelsPath = app_path('Models');
        $modelFiles = glob($modelsPath . '/*.php');

        foreach ($modelFiles as $file) {
            $className = pathinfo($file, PATHINFO_FILENAME);
            $fullClassName = "App\\Models\\{$className}";

            try {
                // Skip if class doesn't exist
                if (!class_exists($fullClassName)) {
                    continue;
                }

                $reflection = new \ReflectionClass($fullClassName);

                // Skip abstract classes, traits or interfaces
                if ($reflection->isAbstract() || $reflection->isTrait() || $reflection->isInterface()) {
                    continue;
                }

                // Skip if not an Eloquent model
                if (!is_subclass_of($fullClassName, 'Illuminate\Database\Eloquent\Model')) {
                    continue;
                }

                // Generate permissions for this model
                $permissionGenerator->generateForModel($fullClassName);
            } catch (\Exception $e) {
                // Skip any files that cause errors during reflection
                continue;
            }
        }


        // Create additional custom permissions
        $customPermissions = [
            [
                'name' => 'manage-settings',
                'section' => 'settings',
                'action' => 'manage',
            ],
            [
                'name' => 'view-dashboard',
                'section' => 'pages',
                'action' => 'view',
            ],

        ];

        foreach ($customPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission['name'],
                'guard_name' => 'admin',
                'section' => $permission['section'],
                'action' => $permission['action']
            ]);
        }

        // Assign all permissions to super-admin
        $superAdminRole = Role::findOrCreate('super-admin', 'admin');
        $superAdminRole->syncPermissions(Permission::all());


    }
}
