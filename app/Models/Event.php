<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'title',
        'slug',
        'type',
        'shortDescription',
        'longDescription',
        'date',
        'endDate',
        'startTime',
        'endTime',
        'venue',
        'isOnline',
        'onlineUrl',
        'price',
        'totalSeats',
        'availableSeats',
        'agenda',
        'image',
        'imageAlt',
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
        'isOnline' => 'boolean',
        'isFeatured' => 'boolean',
        'price' => 'float',
        'totalSeats' => 'integer',
        'availableSeats' => 'integer',
        'agenda' => 'array',
        'seo' => 'array',
        'date' => 'date:Y-m-d',
        'endDate' => 'date:Y-m-d',
    ];

    /**
     * Generate unique slug from title.
     */
    public static function generateSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'event';
        $slug = $base;
        $counter = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $counter++;
            $slug = "{$base}-{$counter}";
        }

        return $slug;
    }

    /**
     * Resolve effective SEO for this event.
     * Uses event's seo if set, else 'event-detail' page SEO default.
     */
    public function resolveSeo(): array
    {
        if (! empty($this->seo) && is_array($this->seo) && (! empty($this->seo['title']) || ! empty($this->seo['description']))) {
            return $this->seo;
        }

        $defaultPageSeo = PageSeo::find('event-detail');
        if ($defaultPageSeo) {
            $seo = $defaultPageSeo->toPayload();
            if (empty($seo['title'])) {
                $seo['title'] = $this->title.' — Events';
            }
            if (empty($seo['description'])) {
                $seo['description'] = $this->shortDescription ?: '';
            }

            return $seo;
        }

        return [
            'title' => $this->title.' — Events',
            'description' => (string) ($this->shortDescription ?? ''),
            'keywords' => (string) ($this->type ?? 'Event'),
            'ogTitle' => $this->title,
            'ogDescription' => (string) ($this->shortDescription ?? ''),
            'ogImage' => (string) ($this->image ?? ''),
            'canonicalUrl' => url("/events/{$this->slug}"),
            'robots' => 'index, follow',
            'imageAltMap' => (object) [],
        ];
    }

    /**
     * Compact payload for list feeds:
     * id, title, slug, type, shortDescription, date, endDate, startTime, endTime, venue, isOnline, price, image
     */
    public function toListPayload(): array
    {
        return [
            'id' => (int) $this->id,
            'title' => (string) $this->title,
            'slug' => (string) $this->slug,
            'type' => (string) ($this->type ?? 'Conference'),
            'shortDescription' => (string) ($this->shortDescription ?? ''),
            'date' => $this->date ? $this->date->format('Y-m-d') : date('Y-m-d'),
            'endDate' => $this->endDate ? $this->endDate->format('Y-m-d') : null,
            'startTime' => (string) ($this->startTime ?? ''),
            'endTime' => (string) ($this->endTime ?? ''),
            'venue' => (string) ($this->venue ?? ''),
            'isOnline' => (bool) $this->isOnline,
            'price' => (float) ($this->price ?? 0),
            'image' => (string) ($this->image ?? ''),
            'imageAlt' => (string) ($this->imageAlt ?? ''),
            'isFeatured' => (bool) $this->isFeatured,
        ];
    }

    /**
     * Full payload for public single event detail:
     * Adds longDescription, totalSeats, availableSeats, agenda, onlineUrl, status, seo
     */
    public function toDetailPayload(): array
    {
        return [
            'id' => (int) $this->id,
            'title' => (string) $this->title,
            'slug' => (string) $this->slug,
            'type' => (string) ($this->type ?? 'Conference'),
            'shortDescription' => (string) ($this->shortDescription ?? ''),
            'longDescription' => (string) ($this->longDescription ?? ''),
            'date' => $this->date ? $this->date->format('Y-m-d') : date('Y-m-d'),
            'endDate' => $this->endDate ? $this->endDate->format('Y-m-d') : null,
            'startTime' => (string) ($this->startTime ?? ''),
            'endTime' => (string) ($this->endTime ?? ''),
            'venue' => (string) ($this->venue ?? ''),
            'isOnline' => (bool) $this->isOnline,
            'onlineUrl' => (string) ($this->onlineUrl ?? ''),
            'price' => (float) ($this->price ?? 0),
            'totalSeats' => (int) ($this->totalSeats ?? 0),
            'availableSeats' => (int) ($this->availableSeats ?? 0),
            'agenda' => is_array($this->agenda) ? array_values($this->agenda) : [],
            'image' => (string) ($this->image ?? ''),
            'imageAlt' => (string) ($this->imageAlt ?? ''),
            'isFeatured' => (bool) $this->isFeatured,
            'status' => (string) ($this->status ?? 'published'),
            'seo' => $this->resolveSeo(),
        ];
    }

    /**
     * Full payload for admin view/edit
     */
    public function toAdminPayload(): array
    {
        return [
            'id' => (int) $this->id,
            'title' => (string) $this->title,
            'slug' => (string) $this->slug,
            'type' => (string) ($this->type ?? 'Conference'),
            'shortDescription' => (string) ($this->shortDescription ?? ''),
            'longDescription' => (string) ($this->longDescription ?? ''),
            'date' => $this->date ? $this->date->format('Y-m-d') : date('Y-m-d'),
            'endDate' => $this->endDate ? $this->endDate->format('Y-m-d') : null,
            'startTime' => (string) ($this->startTime ?? ''),
            'endTime' => (string) ($this->endTime ?? ''),
            'venue' => (string) ($this->venue ?? ''),
            'isOnline' => (bool) $this->isOnline,
            'onlineUrl' => (string) ($this->onlineUrl ?? ''),
            'price' => (float) ($this->price ?? 0),
            'totalSeats' => (int) ($this->totalSeats ?? 0),
            'availableSeats' => (int) ($this->availableSeats ?? 0),
            'agenda' => is_array($this->agenda) ? array_values($this->agenda) : [],
            'image' => (string) ($this->image ?? ''),
            'imageAlt' => (string) ($this->imageAlt ?? ''),
            'isFeatured' => (bool) $this->isFeatured,
            'status' => (string) ($this->status ?? 'published'),
            'seo' => is_array($this->seo) ? $this->seo : (object) [],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
