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

            // User::factory()->create([
            //     'first_name' => 'Test',
            //     'last_name' => 'User',
            //     'email' => 'test@example.com',
            //     'password' => Hash::make('123'),
            //     'username' => 'test',
            //     'phone_no' => '1234567890',
            // ]);
    }
}
