<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Portfolio extends Model
{
    protected $table = 'portfolios';

    protected $fillable = [
        'title',
        'slug',
        'client',
        'year',
        'category',
        'technologies',
        'shortDescription',
        'longDescription',
        'challenge',
        'solution',
        'results',
        'image',
        'imageAlt',
        'gallery',
        'projectUrl',
        'isFeatured',
        'order',
        'isPublished',
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
        'technologies' => 'array',
        'results'      => 'array',
        'gallery'      => 'array',
        'isFeatured'   => 'boolean',
        'isPublished'  => 'boolean',
        'order'        => 'integer',
        'seo'          => 'array',
    ];

    /**
     * Generate unique slug from title.
     */
    public static function generateSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'project';
        $slug = $base;
        $counter = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $counter++;
            $slug = "{$base}-{$counter}";
        }

        return $slug;
    }

    /**
     * Resolve effective SEO for this case study (uses project's seo if set, else 'portfolio-detail' page SEO default).
     */
    public function resolveSeo(): array
    {
        if (! empty($this->seo) && is_array($this->seo) && (! empty($this->seo['title']) || ! empty($this->seo['description']))) {
            return $this->seo;
        }

        $defaultPageSeo = PageSeo::find('portfolio-detail');
        if ($defaultPageSeo) {
            $seo = $defaultPageSeo->toPayload();
            if (empty($seo['title'])) {
                $seo['title'] = $this->title . ' — Case Study';
            }
            if (empty($seo['description'])) {
                $seo['description'] = $this->shortDescription ?: '';
            }
            return $seo;
        }

        return [
            'title'        => $this->title . ' — Case Study',
            'description'  => (string) ($this->shortDescription ?? ''),
            'keywords'     => is_array($this->technologies) ? implode(', ', $this->technologies) : '',
            'ogTitle'      => $this->title,
            'ogDescription'=> (string) ($this->shortDescription ?? ''),
            'ogImage'      => (string) ($this->image ?? ''),
            'canonicalUrl' => url("/portfolio/{$this->slug}"),
            'robots'       => 'index, follow',
            'imageAltMap'  => (object)[],
        ];
    }

    /**
     * Compact payload for catalog listing:
     * id, title, slug, client, year, category, technologies, shortDescription, image, isFeatured
     */
    public function toListPayload(): array
    {
        return [
            'id'               => (int) $this->id,
            'title'            => (string) $this->title,
            'slug'             => (string) $this->slug,
            'client'           => (string) ($this->client ?? ''),
            'year'             => (string) ($this->year ?? ''),
            'category'         => (string) ($this->category ?? 'General'),
            'technologies'     => is_array($this->technologies) ? array_values($this->technologies) : [],
            'shortDescription' => (string) ($this->shortDescription ?? ''),
            'image'            => (string) ($this->image ?? ''),
            'imageAlt'         => (string) ($this->imageAlt ?? ''),
            'isFeatured'       => (bool) $this->isFeatured,
        ];
    }

    /**
     * Full model payload for public case study detail:
     */
    public function toDetailPayload(): array
    {
        return [
            'id'               => (int) $this->id,
            'title'            => (string) $this->title,
            'slug'             => (string) $this->slug,
            'client'           => (string) ($this->client ?? ''),
            'year'             => (string) ($this->year ?? ''),
            'category'         => (string) ($this->category ?? 'General'),
            'technologies'     => is_array($this->technologies) ? array_values($this->technologies) : [],
            'shortDescription' => (string) ($this->shortDescription ?? ''),
            'longDescription'  => (string) ($this->longDescription ?? ''),
            'challenge'        => (string) ($this->challenge ?? ''),
            'solution'         => (string) ($this->solution ?? ''),
            'results'          => is_array($this->results) ? array_values($this->results) : [],
            'image'            => (string) ($this->image ?? ''),
            'imageAlt'         => (string) ($this->imageAlt ?? ''),
            'gallery'          => is_array($this->gallery) ? array_values($this->gallery) : [],
            'projectUrl'       => (string) ($this->projectUrl ?? ''),
            'isFeatured'       => (bool) $this->isFeatured,
            'order'            => (int) $this->order,
            'isPublished'      => (bool) $this->isPublished,
            'seo'              => $this->resolveSeo(),
        ];
    }

    /**
     * Full model payload for admin edit forms:
     */
    public function toAdminPayload(): array
    {
        return [
            'id'               => (int) $this->id,
            'title'            => (string) $this->title,
            'slug'             => (string) $this->slug,
            'client'           => (string) ($this->client ?? ''),
            'year'             => (string) ($this->year ?? ''),
            'category'         => (string) ($this->category ?? 'General'),
            'technologies'     => is_array($this->technologies) ? array_values($this->technologies) : [],
            'shortDescription' => (string) ($this->shortDescription ?? ''),
            'longDescription'  => (string) ($this->longDescription ?? ''),
            'challenge'        => (string) ($this->challenge ?? ''),
            'solution'         => (string) ($this->solution ?? ''),
            'results'          => is_array($this->results) ? array_values($this->results) : [],
            'image'            => (string) ($this->image ?? ''),
            'imageAlt'         => (string) ($this->imageAlt ?? ''),
            'gallery'          => is_array($this->gallery) ? array_values($this->gallery) : [],
            'projectUrl'       => (string) ($this->projectUrl ?? ''),
            'isFeatured'       => (bool) $this->isFeatured,
            'order'            => (int) $this->order,
            'isPublished'      => (bool) $this->isPublished,
            'seo'              => is_array($this->seo) ? $this->seo : (object)[],
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}

