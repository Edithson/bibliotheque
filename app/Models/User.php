<?php

namespace App\Models;

use App\Notifications\QueuedResetPassword;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'google_id', 'avatar', 'type_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }

    public function createdBooks(): HasMany
    {
        return $this->hasMany(Book::class, 'user_id');
    }

    /**
     * Get string role name from type_id (1: guest, 2: auteur, 3: gerant, 4: admin).
     */
    public function getRoleAttribute(): string
    {
        return match ((int) ($this->type_id ?? 1)) {
            4 => 'admin',
            3 => 'gerant',
            2 => 'auteur',
            default => 'guest',
        };
    }

    /**
     * Get numeric role level for hierarchy (1: guest, 2: author, 3: gerant, 4: admin).
     */
    public function getRoleLevelAttribute(): int
    {
        return (int) ($this->type_id ?? 1);
    }

    public function hasRoleLevel(int $level): bool
    {
        return $this->role_level >= $level;
    }

    public function isGuest(): bool
    {
        return $this->role_level === 1;
    }

    public function isAuthor(): bool
    {
        return $this->role_level >= 2;
    }

    public function isGerant(): bool
    {
        return $this->role_level >= 3;
    }

    public function isAdmin(): bool
    {
        return $this->role_level === 4;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new QueuedResetPassword($token));
    }
}
