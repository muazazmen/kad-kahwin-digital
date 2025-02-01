<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestSetting extends Model
{
    /** @use HasFactory<\Database\Factories\GuestSettingFactory> */
    use HasFactory;

    protected $fillable = [
        'overall_attend_limit',
        'attend_limit_per_guest',
        'is_kids',
        'is_timeslot',
        'timeslots',
        'event_id',
    ];

    protected $casts = [
        'timeslots' => 'array',
    ];
}
