<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    /** @use HasFactory<\Database\Factories\ContactFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'is_whatsapp',
        'contact_type',
        'event_id',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
