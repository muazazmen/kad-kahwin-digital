<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate first and last names separately
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        return [
            'first_name' => $firstName, 
            'last_name' => $lastName, 
            'email' => fake()->unique()->safeEmail(), // Generate a unique and safe email
            'email_verified_at' => now(), 
            'password' => static::$password ??= Hash::make('123'), 
            'username' => fake()->userName(), 
            'phone_no' => fake()->phoneNumber(), 
            'remember_token' => Str::random(10), // Generate a random remember token
            'avatar' => "https://avatar.iran.liara.run/username?username={$firstName}+{$lastName}", // Generate avatar URL
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
