<?php

namespace Database\Seeders;

use App\Models\Music;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(5)->create();

        if (!User::where('email', 'muazazmen@gmail.com')->exists()) {
            User::factory()->create([
                'first_name' => 'muaz',
                'last_name' => 'azmen',
                'email' => 'muazazmen@gmail.com',
                'password' => Hash::make('123'),
                'username' => 'muazazmen',
                'phone_no' => '+60166141875',
                'avatar' => 'https://avatar.iran.liara.run/username?username=Muaz+Azmen',
                'role' => 'super_admin',
            ]);

            User::factory()->create([
                'first_name' => 'resepsi',
                'last_name' => '',
                'email' => 'resepsiofficial@gmail.com',
                'password' => Hash::make('123'),
                'username' => 'resepsi',
                'phone_no' => '+60166141875',
                'avatar' => 'https://avatar.iran.liara.run/username?username=Resepsi',
                'role' => 'admin',
            ]);

            $this->call([
                MusicSeeder::class,
                FrameSeeder::class,
                OpeningAnimationSeeder::class,
                EffectSeeder::class,
            ]);
        }
    }
}
