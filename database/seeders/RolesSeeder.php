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
        Role::findOrCreate('super-admin','api');
        Role::findOrCreate('admin','api');
        Role::findOrCreate('customer','api');
        Role::findOrCreate('seller','api');
        Role::findOrCreate('affiliate','api');
    }
}
