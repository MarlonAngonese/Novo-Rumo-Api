<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $original_password = $this->faker->password;
        $password = Hash::make(
            $original_password,
            [
                'rounds' => 10,
                'salt' => env('SALT'),
            ],
        );

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => $password,
            'original_password' => $original_password
        ];
    }
}
