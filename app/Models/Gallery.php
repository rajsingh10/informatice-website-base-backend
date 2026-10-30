<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Gallery extends Model
{
    protected $table = 'galleries';

    protected $fillable = [
        'name',
        'category',
        'description',
        'coverImage',
        'imageAlt',
        'images',
        'order',
        'isFeatured',
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
        'images' => 'array',
        'order' => 'integer',
        'isFeatured' => 'boolean',
        'isPublished' => 'boolean',
    ];

    /**
     * Scope for published gallery albums.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('isPublished', true);
    }

    /**
     * Scope for category filter.
     */
    public function scopeCategory(Builder $query, ?string $category): Builder
    {
        if (blank($category) || strtolower($category) === 'all') {
            return $query;
        }

        return $query->where('category', $category);
    }

    /**
     * Normalize images array ensuring each item has an id, url, title, and caption.
     * Images sent without id are assigned a new unique id.
     */
    public static function normalizeImages(array $images): array
    {
        $normalized = [];
        $nextId = 1;

        // Find existing max id
        foreach ($images as $img) {
            if (is_array($img) && ! empty($img['id']) && is_numeric($img['id'])) {
                $nextId = max($nextId, (int) $img['id'] + 1);
            }
        }

        foreach ($images as $img) {
            if (is_string($img)) {
                $img = ['url' => $img];
            }

            if (! is_array($img) || empty($img['url'])) {
                continue;
            }

            $id = (! empty($img['id']) && is_numeric($img['id'])) ? (int) $img['id'] : $nextId++;

            $normalized[] = [
                'id' => $id,
                'url' => (string) $img['url'],
                'imageAlt' => (string) ($img['imageAlt'] ?? ($img['alt'] ?? ($img['title'] ?? ''))),
                'title' => (string) ($img['title'] ?? ''),
                'caption' => (string) ($img['caption'] ?? ''),
            ];
        }

        return $normalized;
    }

    /**
     * Validation rules for store/update.
     */
    public static function validationRules(bool $isUpdate = false): array
    {
        $prefix = $isUpdate ? 'sometimes|' : '';

        return [
            'name' => "{$prefix}required|string|max:255",
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'coverImage' => 'nullable|string|max:1000',
            'imageAlt' => 'nullable|string|max:500',
            'images' => 'nullable|array',
            'images.*.id' => 'nullable|integer',
            'images.*.url' => 'required_with:images|string|max:1000',
            'images.*.imageAlt' => 'nullable|string|max:500',
            'images.*.title' => 'nullable|string|max:255',
            'images.*.caption' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'isFeatured' => 'nullable|boolean',
            'isPublished' => 'nullable|boolean',
        ];
    }

    /**
     * Resolved list of images.
     */
    public function resolvedImages(): array
    {
        if (! is_array($this->images)) {
            return [];
        }

        return static::normalizeImages($this->images);
    }

    /**
     * Public payload shape defined in Informatic Website 2.0 Backend API Specification.
     */
    public function toPublicPayload(): array
    {
        $images = $this->resolvedImages();
        $cover = $this->coverImage ?: ($images[0]['url'] ?? '');

        return [
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            'category' => (string) ($this->category ?? 'General'),
            'description' => (string) ($this->description ?? ''),
            'coverImage' => (string) $cover,
            'imageAlt' => (string) ($this->imageAlt ?? ''),
            'images' => $images,
            'order' => (int) ($this->order ?? 0),
            'isFeatured' => (bool) $this->isFeatured,
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
