<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SsoProvider extends Model
{
    /** @use HasFactory<\Database\Factories\SsoProviderFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'provider',
        'provider_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
