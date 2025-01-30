<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Font extends Model
{
    /** @use HasFactory<\Database\Factories\FontFactory> */
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
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function organisers()
    {
        return $this->hasMany(Organiser::class);
    }
}
