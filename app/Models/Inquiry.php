<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Module 11: Contact Page API - Inquiry Model
 */
class Inquiry extends Model
{
    protected $table = 'inquiries';

    public const STATUS_NEW      = 'new';
    public const STATUS_READ     = 'read';
    public const STATUS_REPLIED  = 'replied';
    public const STATUS_ARCHIVED = 'archived';

    public static array $allowedStatuses = [
        self::STATUS_NEW,
        self::STATUS_READ,
        self::STATUS_REPLIED,
        self::STATUS_ARCHIVED,
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'serviceInterested',
        'message',
        'status',
        'adminNotes',
        'ipAddress',
        'userAgent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope for status filter.
     */
    public function scopeStatus($query, ?string $status)
    {
        if (! empty($status) && in_array(strtolower($status), static::$allowedStatuses, true)) {
            return $query->where('status', strtolower($status));
        }
        return $query;
    }

    /**
     * Scope for search term (searches name, email, phone, serviceInterested, message).
     */
    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        $term = '%' . trim($term) . '%';
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', $term)
              ->orWhere('email', 'like', $term)
              ->orWhere('phone', 'like', $term)
              ->orWhere('serviceInterested', 'like', $term)
              ->orWhere('message', 'like', $term);
        });
    }

    /**
     * Alias for snake_case attribute compatibility.
     */
    public function setServiceInterestedAttribute($value): void
    {
        $this->attributes['serviceInterested'] = $value;
    }

    /**
     * Return formatted array payload.
     */
    public function toPayload(): array
    {
        return [
            'id'                => $this->id,
            'name'              => (string) $this->name,
            'email'             => (string) $this->email,
            'phone'             => $this->phone ? (string) $this->phone : null,
            'serviceInterested' => $this->serviceInterested ? (string) $this->serviceInterested : null,
            'message'           => (string) $this->message,
            'status'            => (string) ($this->status ?? self::STATUS_NEW),
            'adminNotes'        => $this->adminNotes ? (string) $this->adminNotes : null,
            'ipAddress'         => $this->ipAddress ? (string) $this->ipAddress : null,
            'createdAt'         => $this->created_at?->toISOString(),
            'updatedAt'         => $this->updated_at?->toISOString(),
        ];
    }
}

