<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class FooterConfig extends Model
{
    protected $table = 'footer_configs';

    protected $fillable = [
        'layoutId',
        'cta',
        'grid',
        'links',
        'customCode',
    ];

    protected $casts = [
        'cta'        => 'array',
        'grid'       => 'array',
        'links'      => 'array',
        'customCode' => 'array',
    ];

    public static array $allowedTemplates = [
        'layout1', // Classic 4-Column Corporate (default)
        'layout2', // Modern Dark Bento with Newsletter
        'layout3', // Centered Brand & Social Flow
        'layout4', // Full-width Enterprise Matrix
    ];

    public static function defaultCta(): array
    {
        return [
            'enabled' => true,
            'label'   => 'Schedule Consultation',
            'url'     => '/contact',
        ];
    }

    public static function defaultGrid(): array
    {
        return [
            'columns' => 4,
            'rows'    => 1,
        ];
    }

    public static function defaultItems(): array
    {
        return [
            ['id' => 'foot-1', 'label' => 'Home',             'url' => '/',          'enabled' => true],
            ['id' => 'foot-2', 'label' => 'About Us',         'url' => '/about',     'enabled' => true],
            ['id' => 'foot-3', 'label' => 'Services',         'url' => '/services',  'enabled' => true],
            ['id' => 'foot-4', 'label' => 'Portfolio',        'url' => '/portfolio', 'enabled' => true],
            ['id' => 'foot-5', 'label' => 'Careers',          'url' => '/careers',   'enabled' => true],
            ['id' => 'foot-6', 'label' => 'Blog',             'url' => '/blog',      'enabled' => true],
            ['id' => 'foot-7', 'label' => 'Terms of Service', 'url' => '/terms',     'enabled' => true],
            ['id' => 'foot-8', 'label' => 'Privacy Policy',   'url' => '/privacy',   'enabled' => true],
        ];
    }

    public static function defaultCustomCode(): array
    {
        return [
            'enabled' => false,
            'html'    => '',
            'css'     => '',
            'js'      => '',
        ];
    }

    public static function defaults(): array
    {
        return [
            'layoutId'   => 'layout1',
            'cta'        => static::defaultCta(),
            'grid'       => static::defaultGrid(),
            'links'      => static::defaultItems(),
            'customCode' => static::defaultCustomCode(),
        ];
    }

    public function getItemsAttribute(): array
    {
        return $this->getItemsList();
    }

    public function setItemsAttribute($value): void
    {
        $this->attributes['links'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Get or initialize singleton instance.
     */
    public static function getSingleton(): static
    {
        if (Schema::hasTable('footer_configs')) {
            $config = static::first();
            if (! $config) {
                $config = static::create(static::defaults());
            }
            return $config;
        }

        // Safe fallback in case table not yet created
        $instance = new static();
        $defaults = static::defaults();
        $instance->layoutId = $defaults['layoutId'];
        $instance->cta = $defaults['cta'];
        $instance->grid = $defaults['grid'];
        $instance->items = $defaults['items'];
        $instance->links = $defaults['links'];
        $instance->customCode = $defaults['customCode'];
        return $instance;
    }

    /**
     * Get active footer items (normalized from links).
     */
    public function getItemsList(): array
    {
        $raw = is_array($this->links) && ! empty($this->links)
            ? $this->links
            : static::defaultItems();

        return array_values($raw);
    }

    /**
     * Format payload for public footer.
     */
    public function toPublicPayload(): array
    {
        $layoutRecord = Layout::find('footerLayout');
        $layoutId = $layoutRecord?->templateId ?? $this->layoutId ?? 'layout1';

        $allItems = $this->getItemsList();
        $activeItems = array_values(array_filter($allItems, fn ($item) => ! empty($item['enabled'])));

        $custom = is_array($this->customCode) ? $this->customCode : static::defaultCustomCode();

        return [
            'layoutId'   => $layoutId,
            'layout'     => $layoutId,
            'enabled'    => true,
            'visible'    => true,
            'status'     => 'visible',
            'cta'        => is_array($this->cta) ? $this->cta : static::defaultCta(),
            'grid'       => is_array($this->grid) ? $this->grid : static::defaultGrid(),
            'items'      => $activeItems,
            'links'      => $activeItems,
            'customCode' => [
                'enabled' => ! empty($custom['enabled']),
                'html'    => (string) ($custom['html'] ?? ''),
                'css'     => (string) ($custom['css'] ?? ''),
                'js'      => (string) ($custom['js'] ?? ''),
            ],
        ];
    }

    /**
     * Format payload for admin footer.
     */
    public function toAdminPayload(): array
    {
        $layoutRecord = Layout::find('footerLayout');
        $layoutId = $layoutRecord?->templateId ?? $this->layoutId ?? 'layout1';

        $allItems = $this->getItemsList();
        $custom = is_array($this->customCode) ? $this->customCode : static::defaultCustomCode();

        return [
            'layoutId'         => $layoutId,
            'layout'           => $layoutId,
            'enabled'          => true,
            'visible'          => true,
            'status'           => 'visible',
            'allowedTemplates' => static::$allowedTemplates,
            'cta'              => is_array($this->cta) ? $this->cta : static::defaultCta(),
            'grid'             => is_array($this->grid) ? $this->grid : static::defaultGrid(),
            'items'            => $allItems,
            'links'            => $allItems,
            'customCode'       => [
                'enabled' => ! empty($custom['enabled']),
                'html'    => (string) ($custom['html'] ?? ''),
                'css'     => (string) ($custom['css'] ?? ''),
                'js'      => (string) ($custom['js'] ?? ''),
            ],
        ];
    }
}
