<?php

namespace Database\Seeders;

use App\Models\Frame;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FrameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'muazazmen@gmail.com')->first();

        Frame::factory()->create([
            'title' => 'No Frame',
            'frame_path' => '',
            'created_by' => $user->id,
        ]);
    }
}
