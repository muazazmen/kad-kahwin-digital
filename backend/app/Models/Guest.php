<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    /** @use HasFactory<\Database\Factories\GuestFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'is_attend',
        'total_adults',
        'total_kids',
        'timeslot',
        'event_id',
    ];
}
