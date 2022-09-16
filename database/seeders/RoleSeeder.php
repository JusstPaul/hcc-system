<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maklad\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeder creating the system's handled roles.
     *
     * @return void
     */
    public function run()
    {
        // Reset cache
        app()['cache']->forget('maklad.permission.cache');

        // Create roles
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'instructor']);
        Role::firstOrCreate(['name' => 'student']);
    }
}
