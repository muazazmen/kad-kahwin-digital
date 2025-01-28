<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Music extends Model
{
    /** @use HasFactory<\Database\Factories\MusicFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'artist',
        'album',
        'genre',
        'year',
        'path',
        'created_by',
        'updated_by',
    ];
}
