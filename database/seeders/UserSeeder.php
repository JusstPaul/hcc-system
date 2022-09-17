<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeder creating the admin user.
     *
     * @return void
     */
    public function run()
    {
        // Create user
        $user_password = env('ADMIN_PASSWORD', 'admin');

        $user = User::create([
            'username' => 'admin',
            'password' => Hash::make($user_password),
            'remember_token' => Str::random(10),
        ]);

        // Assign admin role
        $user->assignRole('admin');

        // Create profile
        $user->profile()->create([
            'l_name' => 'Admin',
            'm_name' => null,
            'f_name' => 'System',
            'details' => null,
        ]);
    }
}
