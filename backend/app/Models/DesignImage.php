<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DesignImage extends Model
{
    /** @use HasFactory<\Database\Factories\DesignImageFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'design_id',
        'image_path',
        'is_thumbnail',
    ];

    public function design()
    {
        return $this->belongsTo(Design::class);
    }
}
