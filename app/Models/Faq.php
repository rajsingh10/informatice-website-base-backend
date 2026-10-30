<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $table = 'faqs';

    protected $fillable = [
        'question',
        'answer',
        'category',
        'order',
        'isPublished',
    ];

    protected $casts = [
        'order' => 'integer',
        'isPublished' => 'boolean',
    ];

    /**
     * Scope for published FAQs.
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
     * Scope for search query.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $sub) use ($term) {
            $sub->where('question', 'like', "%{$term}%")
                ->orWhere('answer', 'like', "%{$term}%")
                ->orWhere('category', 'like', "%{$term}%");
        });
    }

    /**
     * Validation rules for store/update.
     */
    public static function validationRules(bool $isUpdate = false): array
    {
        $prefix = $isUpdate ? 'sometimes|' : '';

        return [
            'question' => "{$prefix}required|string|max:500",
            'answer' => "{$prefix}required|string",
            'category' => 'nullable|string|max:100',
            'order' => 'nullable|integer|min:0',
            'isPublished' => 'nullable|boolean',
        ];
    }

    /**
     * Public payload shape defined in Informatic Website 2.0 Backend API Specification.
     */
    public function toPublicPayload(): array
    {
        return [
            'id' => (int) $this->id,
            'question' => (string) $this->question,
            'answer' => (string) $this->answer,
            'category' => (string) ($this->category ?? 'General'),
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
