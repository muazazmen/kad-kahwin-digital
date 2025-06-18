<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Frame extends Model
{
    /** @use HasFactory<\Database\Factories\FrameFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'frame_path',
        'created_by',
        'updated_by',
    ];
}
