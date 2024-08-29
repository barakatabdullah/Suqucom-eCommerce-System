<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all existing roles
        Role::query()->delete();


        // Create main roles
        Role::findOrCreate('super-admin');
        Role::findOrCreate('admin');
        Role::findOrCreate('customer');
        Role::findOrCreate('seller');
        Role::findOrCreate('affiliate');
    }
}
