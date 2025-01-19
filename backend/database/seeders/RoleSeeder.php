<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the default 'user' role only if it doesn't exist
        Role::firstOrCreate([
            'name' => 'user',
        ], [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create the 'admin' role only if it doesn't exist
        Role::firstOrCreate([
            'name' => 'admin',
        ], [
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
