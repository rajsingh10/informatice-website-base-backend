<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $table = 'testimonials';

    protected $fillable = [
        'name',
        'designation',
        'company',
        'photo',
        'rating',
        'text',
        'isFeatured',
        'videoUrl',
        'metric',
        'order',
        'isPublished',
    ];

    protected $casts = [
        'rating' => 'integer',
        'isFeatured' => 'boolean',
        'metric' => 'array',
        'order' => 'integer',
        'isPublished' => 'boolean',
    ];

    /**
     * Default metric structure.
     */
    public static function defaultMetric(): array
    {
        return [
            'label' => '',
            'value' => '',
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
            'designation' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'photo' => 'nullable|string|max:1000',
            'rating' => "{$prefix}required|integer|min:1|max:5",
            'text' => "{$prefix}required|string",
            'isFeatured' => 'nullable|boolean',
            'videoUrl' => 'nullable|string|max:1000',
            'metric' => 'nullable|array',
            'metric.label' => 'nullable|string|max:255',
            'metric.value' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'isPublished' => 'nullable|boolean',
        ];
    }

    /**
     * Scope for published testimonials.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('isPublished', true);
    }

    /**
     * Scope for filtering featured testimonials.
     */
    public function scopeFeatured(Builder $query, ?bool $isFeatured = true): Builder
    {
        if ($isFeatured === null) {
            return $query;
        }

        return $query->where('isFeatured', (bool) $isFeatured);
    }

    /**
     * Scope for filtering by minimum rating.
     */
    public function scopeMinRating(Builder $query, ?int $rating): Builder
    {
        if ($rating === null || $rating < 1) {
            return $query;
        }

        return $query->where('rating', '>=', min(5, $rating));
    }

    /**
     * Resolved metric object.
     */
    public function resolvedMetric(): array
    {
        $defaults = static::defaultMetric();
        if (! is_array($this->metric)) {
            return $defaults;
        }

        return array_merge($defaults, $this->metric);
    }

    /**
     * Public payload shape defined in Informatic Website 2.0 Backend API Specification.
     */
    public function toPublicPayload(): array
    {
        return [
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            'designation' => (string) ($this->designation ?? ''),
            'company' => (string) ($this->company ?? ''),
            'photo' => (string) ($this->photo ?? ''),
            'rating' => (int) ($this->rating ?? 5),
            'text' => (string) $this->text,
            'isFeatured' => (bool) $this->isFeatured,
            'videoUrl' => (string) ($this->videoUrl ?? ''),
            'metric' => $this->resolvedMetric(),
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
