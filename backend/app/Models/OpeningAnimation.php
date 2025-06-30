<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpeningAnimation extends Model
{
    /** @use HasFactory<\Database\Factories\AnimationFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'shadow',
        'left_door',
        'left_door_open',
        'right_door',
        'right_door_open',
        'sealer_position',
        'sealer_style',
        'is_sealer_custom',
        'created_by',
        'updated_by',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class);
    }
}
