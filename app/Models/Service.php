<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Service extends Model
{
    protected $table = 'services';

    protected $fillable = [
        'title',
        'slug',
        'category',
        'shortDescription',
        'fullDescription',
        'icon',
        'image',
        'imageAlt',
        'features',
        'deliverables',
        'order',
        'isFeatured',
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
        'features'     => 'array',
        'deliverables' => 'array',
        'order'        => 'integer',
        'isFeatured'   => 'boolean',
        'isPublished'  => 'boolean',
        'seo'          => 'array',
    ];

    /**
     * Generate unique slug from title.
     */
    public static function generateSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'service';
        $slug = $base;
        $counter = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $counter++;
            $slug = "{$base}-{$counter}";
        }

        return $slug;
    }

    /**
     * Resolve effective SEO for this service (uses service's seo if set, else 'service-detail' page SEO default).
     */
    public function resolveSeo(): array
    {
        if (! empty($this->seo) && is_array($this->seo) && (! empty($this->seo['title']) || ! empty($this->seo['description']))) {
            return $this->seo;
        }

        $defaultPageSeo = PageSeo::find('service-detail');
        if ($defaultPageSeo) {
            $seo = $defaultPageSeo->toPayload();
            // Prepend/replace with service specific title if empty
            if (empty($seo['title'])) {
                $seo['title'] = $this->title;
            }
            if (empty($seo['description'])) {
                $seo['description'] = $this->shortDescription ?: '';
            }
            return $seo;
        }

        return [
            'title'        => $this->title,
            'description'  => (string) ($this->shortDescription ?? ''),
            'keywords'     => '',
            'ogTitle'      => $this->title,
            'ogDescription'=> (string) ($this->shortDescription ?? ''),
            'ogImage'      => (string) ($this->image ?? ''),
            'canonicalUrl' => url("/services/{$this->slug}"),
            'robots'       => 'index, follow',
            'imageAltMap'  => (object)[],
        ];
    }

    /**
     * Compact payload for public catalog listing:
     * id, title, slug, category, shortDescription, icon, image, imageAlt, features
     */
    public function toCatalogPayload(): array
    {
        return [
            'id'               => (int) $this->id,
            'title'            => (string) $this->title,
            'slug'             => (string) $this->slug,
            'category'         => (string) ($this->category ?? 'General'),
            'shortDescription' => (string) ($this->shortDescription ?? ''),
            'icon'             => (string) ($this->icon ?? 'Globe'),
            'image'            => (string) ($this->image ?? ''),
            'imageAlt'         => (string) ($this->imageAlt ?? ''),
            'features'         => is_array($this->features) ? array_values($this->features) : [],
            'isFeatured'       => (bool) $this->isFeatured,
        ];
    }

    /**
     * Full model payload for public detail view:
     */
    public function toDetailPayload(): array
    {
        return [
            'id'               => (int) $this->id,
            'title'            => (string) $this->title,
            'slug'             => (string) $this->slug,
            'category'         => (string) ($this->category ?? 'General'),
            'shortDescription' => (string) ($this->shortDescription ?? ''),
            'fullDescription'  => (string) ($this->fullDescription ?? ''),
            'icon'             => (string) ($this->icon ?? 'Globe'),
            'image'            => (string) ($this->image ?? ''),
            'imageAlt'         => (string) ($this->imageAlt ?? ''),
            'features'         => is_array($this->features) ? array_values($this->features) : [],
            'deliverables'     => is_array($this->deliverables) ? array_values($this->deliverables) : [],
            'order'            => (int) $this->order,
            'isFeatured'       => (bool) $this->isFeatured,
            'isPublished'      => (bool) $this->isPublished,
            'seo'              => $this->resolveSeo(),
        ];
    }

    /**
     * Full payload for admin view & edit form:
     */
    public function toAdminPayload(): array
    {
        return [
            'id'               => (int) $this->id,
            'title'            => (string) $this->title,
            'slug'             => (string) $this->slug,
            'category'         => (string) ($this->category ?? 'General'),
            'shortDescription' => (string) ($this->shortDescription ?? ''),
            'fullDescription'  => (string) ($this->fullDescription ?? ''),
            'icon'             => (string) ($this->icon ?? 'Globe'),
            'image'            => (string) ($this->image ?? ''),
            'imageAlt'         => (string) ($this->imageAlt ?? ''),
            'features'         => is_array($this->features) ? array_values($this->features) : [],
            'deliverables'     => is_array($this->deliverables) ? array_values($this->deliverables) : [],
            'order'            => (int) $this->order,
            'isFeatured'       => (bool) $this->isFeatured,
            'isPublished'      => (bool) $this->isPublished,
            'seo'              => is_array($this->seo) ? $this->seo : (object)[],
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}

