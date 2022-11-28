<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        /* $students = User::factory(50)->create(); */
        /* foreach ($students as $student) { */
        /*   $student->assignRole('student'); */
        /* } */
        /**/
        /* $instructors = User::factory(20)->create(); */
        /* foreach ($instructors as $instructor) { */
        /*   $instructor->assignRole('instructor'); */
        /* } */
    }
}
