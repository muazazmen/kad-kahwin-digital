<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Design extends Model
{
    /** @use HasFactory<\Database\Factories\DesignFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'primary_color',
        'secondary_color',
        'tertiary_color',
        'bg_image',
        'theme_id',
        'created_by',
        'updated_by',
    ];

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }
}
