<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prayer extends Model
{
    /** @use HasFactory<\Database\Factories\PrayerFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'prayer',
        'created_by',
        'updated_by',
    ];
}
