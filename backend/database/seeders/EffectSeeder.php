<?php

namespace Database\Seeders;

use App\Models\Effect;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EffectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::where('email', 'muazazmen@gmail.com')->first();

        // Load the JSON config from the file
        $jsonPath = database_path('data/effects/snow.json');
        $particleConfig = json_decode(file_get_contents($jsonPath), true);

        Effect::insert([
            [
                'name' => 'Salji 1',
                'particle_config' => json_encode($particleConfig),
                'created_by' => $user->id,
                'created_at' => now(),
            ],
        ]);
    }
}
