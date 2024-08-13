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
        $role = Role::firstOrCreate(['name' => 'Super-Admin']);

        $user = User::factory()->create([
            'fname' => 'Super',
            'lname' => 'Admin',
            'email' => 's-admin@suqu.com',
            'password' => bcrypt('s-pa$$W0rd'),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($role);


    }
}
