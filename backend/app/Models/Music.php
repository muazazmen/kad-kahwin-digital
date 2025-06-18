<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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
        'music_path',
        'created_by',
        'updated_by',
    ];

    public function getUrlAttribute($value)
    {
        if ($this->source === 'local') {
            // Generate a public URL for locally stored files
            return Storage::url($value);
        }

        // For YouTube or Spotify, return the URL as is
        return $value;
    }
}
