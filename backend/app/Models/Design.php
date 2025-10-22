<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|Design where(string $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|Design orderBy(string $column, string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Design first()
 */
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

    protected $appends = ['theme_name'];

    public function getThemeNameAttribute()
    {
        return $this->theme?->name;
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected static function booted()
    {
        static::deleting(function ($design) {
            if ($design->isForceDeleting()) {
                // If permanently deleting
                $design->images()->forceDelete();
            } else {
                // If soft deleting
                $design->images()->delete();
            }
        });

        static::restoring(function ($design) {
            // Restore all related images
            $design->images()->withTrashed()->restore();
        });
    }

    public function images()
    {
        return $this->hasMany(DesignImage::class);
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
