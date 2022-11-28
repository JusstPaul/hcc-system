<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use MongoDB\BSON\ObjectId;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
          'username' => $this->faker->name,
          'password' => Hash::make('password'),
          'profile' => [
            '_id' => new ObjectId(),
            'avatar' => null,
            'l_name' => $this->faker->lastName(),
            'm_name' => $this->faker->lastName(),
            'f_name' => $this->faker->firstName(),
            'details' => null
          ]
        ];
    }
}
