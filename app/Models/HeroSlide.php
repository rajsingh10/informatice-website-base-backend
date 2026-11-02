<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    use HasFactory;

    protected $table = 'hero_slides';

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'isFeatured',
        'primaryButtonText',
        'primaryButtonUrl',
        'secondaryButtonText',
        'secondaryButtonUrl',
        'image',
        'imageAlt',
        'order',
        'isPublished',
    ];

    protected $casts = [
        'isFeatured'  => 'boolean',
        'isPublished' => 'boolean',
        'order'       => 'integer',
    ];

    /**
     * Public representation of a hero slide.
     */
    public function toPublicPayload(): array
    {
        return [
            'id'                  => (int) $this->id,
            'title'               => (string) $this->title,
            'subtitle'            => (string) ($this->subtitle ?? $this->description ?? ''),
            'description'         => (string) ($this->description ?? $this->subtitle ?? ''),
            'isFeatured'          => (bool) $this->isFeatured,
            'featured'            => (bool) $this->isFeatured,
            'primaryButtonText'   => (string) ($this->primaryButtonText ?? ''),
            'primaryButtonUrl'    => (string) ($this->primaryButtonUrl ?? ''),
            'secondaryButtonText' => (string) ($this->secondaryButtonText ?? ''),
            'secondaryButtonUrl'  => (string) ($this->secondaryButtonUrl ?? ''),
            'image'               => (string) ($this->image ?? ''),
            'imageAlt'            => (string) ($this->imageAlt ?? ''),
            'order'               => (int) $this->order,
            'isPublished'         => (bool) $this->isPublished,
            'created_at'          => $this->created_at ? $this->created_at->toISOString() : null,
            'updated_at'          => $this->updated_at ? $this->updated_at->toISOString() : null,
        ];
    }

    /**
     * Admin representation of a hero slide.
     */
    public function toAdminPayload(): array
    {
        return $this->toPublicPayload();
    }
}

