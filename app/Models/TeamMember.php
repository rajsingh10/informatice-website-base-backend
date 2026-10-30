<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class TeamMember extends Model
{
    protected $table = 'team_members';

    protected $fillable = [
        'name',
        'designation',
        'department',
        'photo',
        'imageAlt',
        'bio',
        'social',
        'isLeadership',
        'isFeatured',
        'order',
        'isPublished',
    ];

    /**
     * Dynamically filter fillable attributes based on database columns.
     */
    public function getFillable(): array
    {
        $fillable = $this->fillable;
        try {
            if (! Schema::hasColumn($this->getTable(), 'imageAlt')) {
                $fillable = array_values(array_diff($fillable, ['imageAlt']));
            }
        } catch (\Throwable $e) {
            // fallback
        }
        return $fillable;
    }

    /**
     * Get imageAlt attribute safely.
     */
    public function getImageAltAttribute(): string
    {
        if (array_key_exists('imageAlt', $this->attributes) && $this->attributes['imageAlt'] !== null) {
            return (string) $this->attributes['imageAlt'];
        }

        return '';
    }

    protected $casts = [
        'social' => 'array',
        'isLeadership' => 'boolean',
        'isFeatured' => 'boolean',
        'order' => 'integer',
        'isPublished' => 'boolean',
    ];

    /**
     * Default social structure.
     */
    public static function defaultSocial(): array
    {
        return [
            'linkedin' => '',
            'twitter' => '',
            'github' => '',
        ];
    }

    /**
     * Validation rules for store/update.
     */
    public static function validationRules(bool $isUpdate = false): array
    {
        $prefix = $isUpdate ? 'sometimes|' : '';

        return [
            'name' => "{$prefix}required|string|max:255",
            'designation' => "{$prefix}required|string|max:255",
            'department' => 'nullable|string|max:100',
            'photo' => 'nullable|string|max:1000',
            'imageAlt' => 'nullable|string|max:500',
            'bio' => 'nullable|string',
            'social' => 'nullable|array',
            'social.linkedin' => 'nullable|string|max:255',
            'social.twitter' => 'nullable|string|max:255',
            'social.github' => 'nullable|string|max:255',
            'isLeadership' => 'nullable|boolean',
            'isFeatured' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
            'isPublished' => 'nullable|boolean',
        ];
    }

    /**
     * Scope for published team members.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('isPublished', true);
    }

    /**
     * Scope for filtering by department.
     */
    public function scopeDepartment(Builder $query, ?string $dept): Builder
    {
        if (blank($dept) || strtolower($dept) === 'all') {
            return $query;
        }

        return $query->where('department', $dept);
    }

    /**
     * Scope for filtering leadership.
     */
    public function scopeLeadership(Builder $query, ?bool $isLeadership = true): Builder
    {
        if ($isLeadership === null) {
            return $query;
        }

        return $query->where('isLeadership', (bool) $isLeadership);
    }

    /**
     * Clean social array with defaults.
     */
    public function resolvedSocial(): array
    {
        $defaults = static::defaultSocial();
        if (! is_array($this->social)) {
            return $defaults;
        }

        return array_merge($defaults, $this->social);
    }

    /**
     * Public payload shape defined in Informatic Website 2.0 Backend API Specification.
     */
    public function toPublicPayload(): array
    {
        return [
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            'designation' => (string) $this->designation,
            'department' => (string) ($this->department ?? ''),
            'photo' => (string) ($this->photo ?? ''),
            'imageAlt' => (string) ($this->imageAlt ?? ''),
            'bio' => (string) ($this->bio ?? ''),
            'social' => $this->resolvedSocial(),
            'isLeadership' => (bool) $this->isLeadership,
            'isFeatured' => (bool) $this->isFeatured,
            'order' => (int) ($this->order ?? 0),
            'isPublished' => (bool) $this->isPublished,
        ];
    }

    /**
     * Admin payload includes audit timestamps.
     */
    public function toAdminPayload(): array
    {
        $payload = $this->toPublicPayload();
        $payload['createdAt'] = $this->created_at?->toISOString();
        $payload['updatedAt'] = $this->updated_at?->toISOString();

        return $payload;
    }
}
