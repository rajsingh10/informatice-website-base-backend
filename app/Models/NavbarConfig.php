<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavbarConfig extends Model
{
    protected $table = 'navbar_configs';

    protected $fillable = [
        'headerBehavior',
        'cta',
        'items',
    ];

    protected $casts = [
        'cta'   => 'array',
        'items' => 'array',
    ];

    public static array $allowedBehaviors = [
        'fixed',
        'scroll',
        'colored',
        'transparent',
    ];

    /**
     * Default navigation items.
     */
    public static function defaultItems(): array
    {
        return [
            ['id' => 'nav-1', 'label' => 'Home',      'url' => '/',          'enabled' => true],
            ['id' => 'nav-2', 'label' => 'About',     'url' => '/about',     'enabled' => true],
            ['id' => 'nav-3', 'label' => 'Services',  'url' => '/services',  'enabled' => true],
            ['id' => 'nav-4', 'label' => 'Portfolio', 'url' => '/portfolio', 'enabled' => true],
            ['id' => 'nav-5', 'label' => 'Blog',      'url' => '/blog',      'enabled' => true],
            ['id' => 'nav-6', 'label' => 'Events',    'url' => '/events',    'enabled' => true],
            ['id' => 'nav-7', 'label' => 'Contact',   'url' => '/contact',   'enabled' => true],
        ];
    }

    /**
     * Default CTA button configuration.
     */
    public static function defaultCta(): array
    {
        return [
            'enabled' => true,
            'label'   => 'Get a Quote',
            'url'     => '/contact',
        ];
    }

    /**
     * Default configuration map.
     */
    public static function defaults(): array
    {
        return [
            'headerBehavior' => 'fixed',
            'cta'            => static::defaultCta(),
            'items'          => static::defaultItems(),
        ];
    }

    /**
     * Get or initialize the singleton Navbar record.
     */
    public static function getSingleton(): static
    {
        $config = static::first();
        if (! $config) {
            $config = static::create(static::defaults());
        }
        return $config;
    }

    /**
     * Format payload for public header (only enabled: true items).
     */
    public function toPublicPayload(): array
    {
        $items = is_array($this->items) ? $this->items : static::defaultItems();

        $enabledItems = array_values(array_filter($items, function ($item) {
            return ! empty($item['enabled']);
        }));

        return [
            'headerBehavior' => (string) ($this->headerBehavior ?: 'fixed'),
            'cta'            => is_array($this->cta) ? $this->cta : static::defaultCta(),
            'items'          => $enabledItems,
        ];
    }

    /**
     * Format payload for admin header (includes disabled items).
     */
    public function toAdminPayload(): array
    {
        return [
            'headerBehavior' => (string) ($this->headerBehavior ?: 'fixed'),
            'cta'            => is_array($this->cta) ? $this->cta : static::defaultCta(),
            'items'          => is_array($this->items) ? array_values($this->items) : static::defaultItems(),
        ];
    }

    /**
     * Process array of items: assign ID if missing, preserve order, normalize boolean enabled.
     */
    public static function processItems(array $rawItems): array
    {
        $existingIds = [];
        $maxNum = 0;

        foreach ($rawItems as $item) {
            if (! empty($item['id']) && preg_match('/^nav-(\d+)$/', $item['id'], $m)) {
                $num = (int) $m[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
                $existingIds[$item['id']] = true;
            }
        }

        $processed = [];
        foreach ($rawItems as $item) {
            $id = ! empty($item['id']) ? (string) $item['id'] : null;

            if (! $id) {
                $maxNum++;
                $id = "nav-{$maxNum}";
            }

            $processed[] = [
                'id'      => $id,
                'label'   => (string) ($item['label'] ?? 'Link'),
                'url'     => (string) ($item['url'] ?? '/'),
                'enabled' => isset($item['enabled']) ? (bool) $item['enabled'] : true,
            ];
        }

        return $processed;
    }
}

