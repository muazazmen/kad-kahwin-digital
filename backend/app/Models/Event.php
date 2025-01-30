<?php

namespace App\Models;

use App\EventStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'introduction',
        'greeting',
        'date',
        'start_at',
        'end_at',
        'venue_name',
        'venue_address',
        'google_map_url',
        'phone_no_1',
        'phone_no_2',
        'phone_no_3',
        'phone_no_4',
        'status',
        'organiser_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => EventStatus::class,
    ];

    public function organiser()
    {
        return $this->belongsTo(Organiser::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function tentatives()
    {
        return $this->hasMany(Tentative::class);
    }

    public function gifts()
    {
        return $this->has(Gift::class);
    }
}
