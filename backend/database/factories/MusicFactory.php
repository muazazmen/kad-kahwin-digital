<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Music>
 */
class MusicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->name(),
            'artist' => fake()->name(),
            'album' => fake()->name(),
            'genre' => fake()->name(),
            'year' => fake()->name(),
            'music_path' => fake()->url(),
        ];
    }
}
