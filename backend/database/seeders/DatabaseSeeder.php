<?php

namespace Database\Seeders;

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

        if (!User::where('email', 'muhammad@example.com')->exists()) {
            User::factory()->create([
                'first_name' => 'Muhammad',
                'last_name' => 'Abdullah',
                'email' => 'muhammad@example.com',
                'password' => Hash::make('123'),
                'username' => 'muhammad',
                'phone_no' => '1234567890',
                'avatar' => 'https://avatar.iran.liara.run/username?username=Muhammad+Abdullah',
                'role' => 'admin',
            ]);
        }
    }
}
