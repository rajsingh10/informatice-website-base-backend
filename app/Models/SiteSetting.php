<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $table = 'site_settings';

    protected $fillable = [
        'siteName',
        'logoUrl',
        'faviconUrl',
        'email',
        'phone',
        'address',
        'mapEmbedUrl',
        'offices',
        'socials',
        'announcement',
    ];

    protected $casts = [
        'offices'      => 'array',
        'socials'      => 'array',
        'announcement' => 'array',
    ];

    /**
     * Default settings payload.
     */
    public static function defaults(): array
    {
        return [
            'siteName'     => 'Acme',
            'logoUrl'      => '',
            'faviconUrl'   => '',
            'email'        => '',
            'phone'        => '',
            'address'      => '',
            'mapEmbedUrl'  => '',
            'offices'      => [
                [
                    'name'    => 'HQ',
                    'address' => '',
                    'phone'   => '',
                    'email'   => '',
                ],
            ],
            'socials'      => [
                'facebook'  => '',
                'instagram' => '',
                'linkedin'  => '',
                'twitter'   => '',
                'youtube'   => '',
            ],
            'announcement' => [
                'enabled' => false,
                'text'    => '',
                'url'     => '',
            ],
        ];
    }

    /**
     * Get or create the singleton settings record.
     */
    public static function getSingleton(): static
    {
        $setting = static::first();
        if (! $setting) {
            $setting = static::create(static::defaults());
        }
        return $setting;
    }

    /**
     * Format payload.
     */
    public function toPayload(): array
    {
        $defaults = static::defaults();

        return [
            'siteName'     => (string) ($this->siteName ?? $defaults['siteName']),
            'logoUrl'      => (string) ($this->logoUrl ?? ''),
            'faviconUrl'   => (string) ($this->faviconUrl ?? ''),
            'email'        => (string) ($this->email ?? ''),
            'phone'        => (string) ($this->phone ?? ''),
            'address'      => (string) ($this->address ?? ''),
            'mapEmbedUrl'  => (string) ($this->mapEmbedUrl ?? ''),
            'offices'      => is_array($this->offices) ? $this->offices : $defaults['offices'],
            'socials'      => is_array($this->socials) ? array_merge($defaults['socials'], $this->socials) : $defaults['socials'],
            'announcement' => is_array($this->announcement) ? array_merge($defaults['announcement'], $this->announcement) : $defaults['announcement'],
        ];
    }
}

