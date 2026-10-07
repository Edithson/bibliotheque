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
        'user_id',
        'updated_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'price' => 'integer',
            'nbr_pages' => 'integer',
        ];
    }

    public function getIsFreeAttribute(): bool
    {
        return $this->price === 0;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }
}
