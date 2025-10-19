<?php

namespace Database\Seeders;

use App\Models\OpeningAnimation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OpeningAnimationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::where('email', 'muazazmen@gmail.com')->first();

        OpeningAnimation::insert([
            [
                'name' => 'Pintu 1',
                'shadow' => 'absolute bg-transparent top-1/2 left-0 -translate-y-1/2 w-1/2 h-3/4 rounded-full shadow-[8px_10px_30px_20px_rgba(0,0,0,0.2)] z-[11]',
                'doors_color' => '#c2acf8',
                'left_door' => 'relative w-1/2 h-full origin-left transition-all ease-in-out duration-[2000ms] delay-500 border-r border-surface-400 z-[11]',
                'left_door_open' => '[transform:rotateY(150deg)]',
                'right_door' => 'w-1/2 h-full origin-right transition-all ease-in-out duration-[2000ms] delay-500 z-10',
                'right_door_open' => '[transform:rotateY(150deg)]',
                'sealer_position' => 'absolute top-1/2 right-0 transform translate-x-1/2 -translate-y-1/2 transition-opacity duration-75',
                'sealer_style' => 'rounded-full w-32 h-32 flex flex-col items-center justify-center shadow-[0px_0px_15px_rgba(0,0,0,0.7)] pulse-animation',
                'is_sealer_custom' => true,
                'created_by' => $user->id,
                'created_at' => now(),
            ],
            // [
            //     'name' => 'Fancy Animation',
            //     'shadow' => 'fancy-shadow.png',
            //     'left_door' => 'fancy-left-door.png',
            //     'left_door_open' => 'fancy-left-door-open.png',
            //     'right_door' => 'fancy-right-door.png',
            //     'right_door_open' => 'fancy-right-door-open.png',
            //     'sealer_position' => 'top-1/2 right-0 translate-x-1/2',
            //     'sealer_style' => 'rounded-full w-32 h-32 bg-blue-500',
            //     'is_sealer_custom' => true,
            // ],
        ]);
    }
}
