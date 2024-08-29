<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Ensure the Super-Admin role exists
        $role = Role::findOrCreate('super-admin','api');

        $user = User::updateOrCreate(
            ['email' => 's-admin@suqu.com'],
            [
                'fname' => 'Super',
                'lname' => 'Admin',
                'password' => bcrypt('s-pa$$W0rd'),
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole($role);


    }
}
