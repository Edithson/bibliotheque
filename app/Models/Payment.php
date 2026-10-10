<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'book_id',
        'amount',
        'status',
        'transaction_id',
        'payment_ref',
        'phone_number',
        'operator',
        'payment_details',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'payment_details' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }
}
