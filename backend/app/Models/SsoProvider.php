<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SsoProvider extends Model
{
    /** @use HasFactory<\Database\Factories\SsoProviderFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'provider',
        'provider_id',
        'user_id',
    ];
}
