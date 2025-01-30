<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organiser extends Model
{
    /** @use HasFactory<\Database\Factories\OrganiserFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'short_name_1', // nama pendek pengantin lelaki / nama pendek pasangan 1
        'short_name_2', // nama pendek pengantin perempuan / nama pendek pasangan 2
        'short_name_3', // nama pendek pengantin lelaki 2
        'short_name_4', // nama pendek pengantin perempuan 2
        'full_name_1', // nama penuh pengantin lelaki / nama penuh pasangan 1
        'full_name_2', // nama penuh pengantin perempuan / nama penuh pasangan 2
        'full_name_3', // nama penuh pengantin lelaki 2
        'full_name_4', // nama penuh pengantin perempuan 2
        'associated_name_1', // nama bapa pengantin lelaki / nama makayah pasangan 1
        'associated_name_1_1', // nama ibu pengantin lelaki 
        'associated_name_2', // nama bapa pengantin perempuan / nama makayah pasangan 2
        'associated_name_2_1', // nama ibu pengantin perempuan
        'organiser_type', // single couple, double couple
        'image_1',
        'font_id', 
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

    public function font()
    {
        return $this->belongsTo(Font::class);
    }

    public function events()
    {
        return $this->has(Event::class);
    }
}
