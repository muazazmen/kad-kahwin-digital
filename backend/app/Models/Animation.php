<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Animation extends Model
{
    /** @use HasFactory<\Database\Factories\AnimationFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'url',
        'path',
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
