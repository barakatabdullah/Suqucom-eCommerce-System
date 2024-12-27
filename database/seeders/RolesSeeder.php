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
        Role::findOrCreate('super-admin','admin');
        Role::findOrCreate('admin','admin');
        Role::findOrCreate('customer','admin');
        Role::findOrCreate('seller','admin');
        Role::findOrCreate('affiliate','admin');
    }
}
