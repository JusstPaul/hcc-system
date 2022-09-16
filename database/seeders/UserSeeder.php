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
    public function void()
    {
        $user_password = env('ADMIN_PASSWORD', 'admin');

        $user = User::create([
            'username' => 'admin',
            'password' => Hash::make($user_password),
            'remember_token' => Str::random(10),
        ]);

        $user->assignRole('admin');
    }
}
