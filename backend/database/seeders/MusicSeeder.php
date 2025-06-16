<?php

namespace Database\Seeders;

use App\Models\Music;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MusicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'muazazmen@gmail.com')->first();

        Music::factory()->create([
            'title' => 'Youtube',
            'artist' => '',
            'album' => '',
            'genre' => '',
            'year' => '',
            'url' => '',
            'created_by' => $user->id,
        ]);
    }
}
