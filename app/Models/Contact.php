<?php

namespace App\Models;

use Database\Factories\ContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    /** @use HasFactory<ContactFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'is_read',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    /**
     * Get human-readable subject label.
     */
    public function getSubjectLabelAttribute(): string
    {
        return match ($this->subject) {
            'author_request' => '✍️ Demande pour devenir Auteur',
            'support' => '🛠️ Support Technique / Téléchargement',
            default => '💬 Question Générale',
        };
    }
}
