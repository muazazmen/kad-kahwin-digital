<?php

namespace Database\Seeders;

use App\Models\Theme;
use App\Models\User;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'muazazmen@gmail.com')->first();

        if (!$user) {
            $this->command->warn('⚠️ User with email muazazmen@gmail.com not found. Skipping theme seeding.');
            return;
        }

        // ✅ List of themes to seed
        $themes = [
            'Cartoon',
            'Elegant',
            'Minimalist',
            'Classic',
            'Modern',
            'Floral',
        ];

        foreach ($themes as $themeName) {
            Theme::factory()->create([
                'name' => $themeName,
                'created_by' => $user->id,
            ]);
        }

        $this->command->info('🎨 Themes seeded successfully!');
    }
}
