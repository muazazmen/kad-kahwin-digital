<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'introduction',
        'greeting',
        'date',
        'start_at',
        'end_at',
        'venue_name',
        'venue_address',
        'google_map_url',
        'phone_no_1',
        'phone_no_2',
        'phone_no_3',
        'phone_no_4',
        'created_by',
        'updated_by',
    ];
}
