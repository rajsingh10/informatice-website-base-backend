<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSeo extends Model
{
    protected $table = 'page_seos';

    protected $primaryKey = 'page';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'page',
        'title',
        'description',
        'keywords',
        'ogTitle',
        'ogDescription',
        'ogImage',
        'canonicalUrl',
        'robots',
        'imageAltMap',
    ];

    protected $casts = [
        'imageAltMap' => 'array',
    ];

    /**
     * All recognized :page keys (original scope + recommended additions).
     */
    public static array $validPages = [
        // Original scope
        'home',
        'services',
        'service-detail',
        'portfolio',
        'portfolio-detail',
        'blog',
        'blog-detail',
        'events',
        'event-detail',
        'team',
        'contact',
        'career',
        // Recommended additions
        'testimonials',
        'gallery',
        'faq',
        'terms',
        'privacy',
    ];

    public static function isValidPage(string $page): bool
    {
        return in_array($page, static::$validPages);
    }

    /**
     * Empty SEO defaults for when a page has no record.
     */
    public static function defaults(string $page = ''): array
    {
        return [
            'title'        => '',
            'description'  => '',
            'keywords'     => '',
            'ogTitle'      => '',
            'ogDescription'=> '',
            'ogImage'      => '',
            'canonicalUrl' => '',
            'robots'       => 'index, follow',
            'imageAltMap'  => (object)[],
        ];
    }

    /**
     * Format model data into payload shape.
     */
    public function toPayload(): array
    {
        return [
            'title'        => (string) ($this->title ?? ''),
            'description'  => (string) ($this->description ?? ''),
            'keywords'     => (string) ($this->keywords ?? ''),
            'ogTitle'      => (string) ($this->ogTitle ?? ''),
            'ogDescription'=> (string) ($this->ogDescription ?? ''),
            'ogImage'      => (string) ($this->ogImage ?? ''),
            'canonicalUrl' => (string) ($this->canonicalUrl ?? ''),
            'robots'       => (string) ($this->robots ?? 'index, follow'),
            'imageAltMap'  => $this->imageAltMap ?: (object)[],
        ];
    }
}

