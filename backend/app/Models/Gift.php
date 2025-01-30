<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gift extends Model
{
    /** @use HasFactory<\Database\Factories\GiftFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bank_name',
        'bank_account_no',
        'qr_image',
        'event_id',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
