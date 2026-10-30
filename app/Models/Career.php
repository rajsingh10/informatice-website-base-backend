<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Career extends Model
{
    protected $table = 'careers';

    protected $fillable = [
        'title',
        'slug',
        'department',
        'location',
        'type',
        'experience',
        'description',
        'requirements',
        'applyUrl',
        'status',
        'order',
        'postedAt',
    ];

    protected $casts = [
        'requirements' => 'array',
        'order' => 'integer',
        'postedAt' => 'datetime',
    ];

    public static array $types = ['full-time', 'part-time', 'contract', 'internship'];

    public static array $statuses = ['open', 'closed'];

    protected static function booted(): void
    {
        static::creating(function (Career $career) {
            if (empty($career->slug) && ! empty($career->title)) {
                $career->slug = static::generateUniqueSlug($career->title);
            }
        });
    }

    /**
     * Scope for public open positions.
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope for department filter.
     */
    public function scopeDepartment(Builder $query, ?string $department): Builder
    {
        if (blank($department) || strtolower($department) === 'all') {
            return $query;
        }

        return $query->where('department', $department);
    }

    /**
     * Scope for job type filter.
     */
    public function scopeType(Builder $query, ?string $type): Builder
    {
        if (blank($type) || strtolower($type) === 'all') {
            return $query;
        }

        return $query->where('type', $type);
    }

    /**
     * Scope for location filter.
     */
    public function scopeLocation(Builder $query, ?string $location): Builder
    {
        if (blank($location) || strtolower($location) === 'all') {
            return $query;
        }

        return $query->where('location', 'like', "%{$location}%");
    }

    /**
     * Scope for search query.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $sub) use ($term) {
            $sub->where('title', 'like', "%{$term}%")
                ->orWhere('department', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Generate unique slug from title.
     */
    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        if (blank($base)) {
            $base = 'career-'.time();
        }

        $slug = $base;
        $counter = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Validation rules for store/update.
     */
    public static function validationRules(bool $isUpdate = false, ?int $ignoreId = null): array
    {
        $prefix = $isUpdate ? 'sometimes|' : '';
        $slugRule = $isUpdate
            ? "sometimes|string|max:255|unique:careers,slug,{$ignoreId}"
            : 'nullable|string|max:255|unique:careers,slug';

        return [
            'title' => "{$prefix}required|string|max:255",
            'slug' => $slugRule,
            'department' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'type' => 'nullable|string|in:full-time,part-time,contract,internship',
            'experience' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string|max:500',
            'applyUrl' => 'nullable|string|max:1000',
            'status' => 'nullable|string|in:open,closed',
            'order' => 'nullable|integer|min:0',
            'postedAt' => 'nullable|date',
        ];
    }

    /**
     * Public payload shape defined in Informatic Website 2.0 Backend API Specification.
     */
    public function toPublicPayload(): array
    {
        return [
            'id' => (int) $this->id,
            'title' => (string) $this->title,
            'slug' => (string) $this->slug,
            'department' => (string) ($this->department ?? 'General'),
            'location' => (string) ($this->location ?? 'Remote'),
            'type' => (string) ($this->type ?? 'full-time'),
            'experience' => (string) ($this->experience ?? ''),
            'description' => (string) ($this->description ?? ''),
            'requirements' => is_array($this->requirements) ? array_values($this->requirements) : [],
            'applyUrl' => (string) ($this->applyUrl ?? ''),
            'status' => (string) ($this->status ?? 'open'),
            'order' => (int) ($this->order ?? 0),
            'postedAt' => $this->postedAt ? $this->postedAt->toISOString() : ($this->created_at ? $this->created_at->toISOString() : null),
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
