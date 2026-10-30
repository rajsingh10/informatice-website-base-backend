<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalPage extends Model
{
    protected $table = 'legal_pages';

    protected $fillable = [
        'type',
        'title',
        'content',
        'lastUpdated',
    ];

    public static array $types = ['terms', 'privacy'];

    public static function isValidType(string $type): bool
    {
        return in_array(strtolower($type), static::$types, true);
    }

    /**
     * Payload structure for public and admin.
     */
    public function toPayload(): array
    {
        return [
            'type' => (string) $this->type,
            'title' => (string) $this->title,
            'content' => (string) $this->content,
            'lastUpdated' => (string) ($this->lastUpdated ?: ($this->updated_at ? $this->updated_at->toDateString() : now()->toDateString())),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
