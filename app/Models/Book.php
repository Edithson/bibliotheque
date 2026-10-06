<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'author',
        'category_id',
        'price',
        'cover_color',
        'cover_width',
        'cover_height',
        'description',
        'excerpt',
        'file_path',
        'nbr_pages',
        'publish_year',
        'is_published',
    ];

    public function getIsFreeAttribute(): bool
    {
        return $this->price === 0;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }
}
