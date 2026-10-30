<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Blog extends Model
{
    protected $table = 'blogs';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'imageAlt',
        'category',
        'author',
        'date',
        'readTime',
        'tags',
        'isFeatured',
        'status',
        'seo',
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
     * Get imageAlt with graceful fallback to seo.imageAlt if column not present.
     */
    public function getImageAltAttribute(): string
    {
        if (array_key_exists('imageAlt', $this->attributes) && $this->attributes['imageAlt'] !== null) {
            return (string) $this->attributes['imageAlt'];
        }

        if (is_array($this->seo) && ! empty($this->seo['imageAlt'])) {
            return (string) $this->seo['imageAlt'];
        }

        return '';
    }

    protected $casts = [
        'author'     => 'array',
        'tags'       => 'array',
        'isFeatured' => 'boolean',
        'seo'        => 'array',
        'date'       => 'date:Y-m-d',
    ];

    /**
     * Generate unique slug from title.
     */
    public static function generateSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'post';
        $slug = $base;
        $counter = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $counter++;
            $slug = "{$base}-{$counter}";
        }

        return $slug;
    }

    /**
     * Compute read time from HTML content (avg 200 words/min).
     */
    public static function calculateReadTime(?string $content): string
    {
        $words = str_word_count(strip_tags((string) $content));
        $minutes = max(1, (int) ceil($words / 200));
        return "{$minutes} min";
    }

    /**
     * Resolve effective SEO for this article.
     * Uses post's seo if set, else 'blog-detail' page SEO default.
     */
    public function resolveSeo(): array
    {
        if (! empty($this->seo) && is_array($this->seo) && (! empty($this->seo['title']) || ! empty($this->seo['description']))) {
            return $this->seo;
        }

        $defaultPageSeo = PageSeo::find('blog-detail');
        if ($defaultPageSeo) {
            $seo = $defaultPageSeo->toPayload();
            if (empty($seo['title'])) {
                $seo['title'] = $this->title . ' — Blog';
            }
            if (empty($seo['description'])) {
                $seo['description'] = $this->excerpt ?: '';
            }
            return $seo;
        }

        return [
            'title'        => $this->title . ' — Blog',
            'description'  => (string) ($this->excerpt ?? ''),
            'keywords'     => is_array($this->tags) ? implode(', ', $this->tags) : '',
            'ogTitle'      => $this->title,
            'ogDescription'=> (string) ($this->excerpt ?? ''),
            'ogImage'      => (string) ($this->image ?? ''),
            'canonicalUrl' => url("/blog/{$this->slug}"),
            'robots'       => 'index, follow',
            'imageAltMap'  => (object)[],
        ];
    }

    /**
     * Compact payload for public feed:
     * id, title, slug, excerpt, image, category, author { name, avatar }, date, readTime, tags
     */
    public function toListPayload(): array
    {
        return [
            'id'        => (int) $this->id,
            'title'     => (string) $this->title,
            'slug'      => (string) $this->slug,
            'excerpt'   => (string) ($this->excerpt ?? ''),
            'image'     => (string) ($this->image ?? ''),
            'imageAlt'  => (string) ($this->imageAlt ?? ''),
            'category'  => (string) ($this->category ?? 'General'),
            'author'    => [
                'name'   => $this->author['name'] ?? '',
                'avatar' => $this->author['avatar'] ?? '',
            ],
            'date'      => $this->date ? $this->date->format('Y-m-d') : date('Y-m-d'),
            'readTime'  => (string) ($this->readTime ?? '1 min'),
            'tags'      => is_array($this->tags) ? array_values($this->tags) : [],
            'isFeatured'=> (bool) $this->isFeatured,
        ];
    }

    /**
     * Full payload for public article detail:
     * Adds content and author.designation
     */
    public function toDetailPayload(): array
    {
        return [
            'id'        => (int) $this->id,
            'title'     => (string) $this->title,
            'slug'      => (string) $this->slug,
            'excerpt'   => (string) ($this->excerpt ?? ''),
            'content'   => (string) ($this->content ?? ''),
            'image'     => (string) ($this->image ?? ''),
            'imageAlt'  => (string) ($this->imageAlt ?? ''),
            'category'  => (string) ($this->category ?? 'General'),
            'author'    => [
                'name'        => $this->author['name'] ?? '',
                'designation' => $this->author['designation'] ?? '',
                'avatar'      => $this->author['avatar'] ?? '',
            ],
            'date'      => $this->date ? $this->date->format('Y-m-d') : date('Y-m-d'),
            'readTime'  => (string) ($this->readTime ?? '1 min'),
            'tags'      => is_array($this->tags) ? array_values($this->tags) : [],
            'isFeatured'=> (bool) $this->isFeatured,
            'status'    => (string) ($this->status ?? 'published'),
            'seo'       => $this->resolveSeo(),
        ];
    }

    /**
     * Full model payload for admin edit/view:
     */
    public function toAdminPayload(): array
    {
        return [
            'id'         => (int) $this->id,
            'title'      => (string) $this->title,
            'slug'       => (string) $this->slug,
            'excerpt'    => (string) ($this->excerpt ?? ''),
            'content'    => (string) ($this->content ?? ''),
            'image'      => (string) ($this->image ?? ''),
            'imageAlt'   => (string) ($this->imageAlt ?? ''),
            'category'   => (string) ($this->category ?? 'General'),
            'author'     => [
                'name'        => $this->author['name'] ?? '',
                'designation' => $this->author['designation'] ?? '',
                'avatar'      => $this->author['avatar'] ?? '',
            ],
            'date'       => $this->date ? $this->date->format('Y-m-d') : date('Y-m-d'),
            'readTime'   => (string) ($this->readTime ?? '1 min'),
            'tags'       => is_array($this->tags) ? array_values($this->tags) : [],
            'isFeatured' => (bool) $this->isFeatured,
            'status'     => (string) ($this->status ?? 'published'),
            'seo'        => is_array($this->seo) ? $this->seo : (object)[],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

