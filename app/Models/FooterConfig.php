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
            'enabled'  => true,
            'title'    => "Let's work together to bring your vision to life!",
            'subtitle' => "Get a free consultation and quote today.",
            'label'    => 'Schedule Consultation',
            'url'      => '/contact',
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
            // Quick Links
            ['id' => 'foot-1',  'category' => 'quick_links',  'label' => 'Home',                   'url' => '/',                            'enabled' => true],
            ['id' => 'foot-2',  'category' => 'quick_links',  'label' => 'About',                  'url' => '/about',                       'enabled' => true],
            ['id' => 'foot-3',  'category' => 'quick_links',  'label' => 'Services',               'url' => '/services',                    'enabled' => true],
            ['id' => 'foot-4',  'category' => 'quick_links',  'label' => 'Portfolio',              'url' => '/portfolio',                   'enabled' => true],
            ['id' => 'foot-5',  'category' => 'quick_links',  'label' => 'Team',                   'url' => '/team',                        'enabled' => true],
            ['id' => 'foot-6',  'category' => 'quick_links',  'label' => 'Blog',                   'url' => '/blog',                        'enabled' => true],

            // Our Services
            ['id' => 'foot-7',  'category' => 'services',     'label' => 'Web Development',        'url' => '/services/web-development',    'enabled' => true],
            ['id' => 'foot-8',  'category' => 'services',     'label' => 'Mobile App Development', 'url' => '/services/mobile-app-development', 'enabled' => true],
            ['id' => 'foot-9',  'category' => 'services',     'label' => 'UI/UX Design',           'url' => '/services/ui-ux-design',       'enabled' => true],
            ['id' => 'foot-10', 'category' => 'services',     'label' => 'Digital Marketing',      'url' => '/services/digital-marketing',  'enabled' => true],
            ['id' => 'foot-11', 'category' => 'services',     'label' => 'Cloud Solutions',        'url' => '/services/cloud-solutions',    'enabled' => true],
            ['id' => 'foot-12', 'category' => 'services',     'label' => 'Brand Identity',         'url' => '/services/brand-identity',     'enabled' => true],
        ];
    }

    public static function defaultBrand(): array
    {
        return [
            'name'        => 'NEXGEN SOLUTIONS',
            'tagline'     => 'Transforming Ideas Into Digital Reality',
            'socials'     => [
                ['platform' => 'facebook',  'icon' => 'fa-brands fa-facebook-f', 'url' => 'https://facebook.com/nexgen', 'enabled' => true],
                ['platform' => 'twitter',   'icon' => 'fa-brands fa-twitter',    'url' => 'https://twitter.com/nexgen',  'enabled' => true],
                ['platform' => 'instagram', 'icon' => 'fa-brands fa-instagram',  'url' => 'https://instagram.com/nexgen','enabled' => true],
                ['platform' => 'linkedin',  'icon' => 'fa-brands fa-linkedin-in','url' => 'https://linkedin.com/company/nexgen','enabled' => true],
            ],
        ];
    }

    public static function defaultContact(): array
    {
        return [
            'address' => '42, Tech Park, Sector 62, Noida, UP - 201301',
            'email'   => 'hello@nexgensolutions.com',
            'phone'   => '+91 98765 43210',
        ];
    }

    public static function defaultBottom(): array
    {
        return [
            'copyright' => '© ' . date('Y') . ' NexGen Solutions. All rights reserved.',
            'links'     => [
                ['label' => 'Privacy Policy',   'url' => '/privacy'],
                ['label' => 'Terms of Service', 'url' => '/terms'],
                ['label' => 'Contact',          'url' => '/contact'],
            ],
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
            'items'      => static::defaultItems(),
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
        try {
            if (Schema::hasTable('footer_configs')) {
                $config = static::first();
                if (! $config) {
                    $config = static::create(static::defaults());
                }
                return $config;
            }
        } catch (\Throwable $e) {
            // Fallback below if table not yet created or DB error
        }

        // Safe fallback in case table not yet created
        $instance = new static();
        $defaults = static::defaults();
        $instance->layoutId   = $defaults['layoutId'] ?? 'layout1';
        $instance->cta        = $defaults['cta'] ?? static::defaultCta();
        $instance->grid       = $defaults['grid'] ?? static::defaultGrid();
        $instance->links      = $defaults['links'] ?? static::defaultItems();
        $instance->customCode = $defaults['customCode'] ?? static::defaultCustomCode();
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
     * Build 4-column structured footer layout.
     */
    public function buildColumns(array $items): array
    {
        $quickLinks = [];
        $services   = [];

        foreach ($items as $item) {
            $cat = $item['category'] ?? '';
            if ($cat === 'services' || str_contains(strtolower($item['url'] ?? ''), '/services/')) {
                $services[] = $item;
            } else {
                $quickLinks[] = $item;
            }
        }

        if (empty($services)) {
            $half = (int) ceil(count($items) / 2);
            $quickLinks = array_slice($items, 0, $half);
            $services   = array_slice($items, $half);
        }

        $brand = static::defaultBrand();
        $contact = static::defaultContact();

        return [
            [
                'id'          => 'col-1',
                'type'        => 'brand',
                'title'       => $brand['name'],
                'description' => $brand['tagline'],
                'socials'     => $brand['socials'],
            ],
            [
                'id'    => 'col-2',
                'type'  => 'links',
                'title' => 'QUICK LINKS',
                'items' => $quickLinks,
            ],
            [
                'id'    => 'col-3',
                'type'  => 'links',
                'title' => 'OUR SERVICES',
                'items' => $services,
            ],
            [
                'id'      => 'col-4',
                'type'    => 'contact',
                'title'   => 'GET IN TOUCH',
                'address' => $contact['address'],
                'email'   => $contact['email'],
                'phone'   => $contact['phone'],
            ],
        ];
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
            'brand'      => static::defaultBrand(),
            'cta'        => is_array($this->cta) ? array_merge(static::defaultCta(), $this->cta) : static::defaultCta(),
            'grid'       => is_array($this->grid) ? $this->grid : static::defaultGrid(),
            'columns'    => $this->buildColumns($activeItems),
            'items'      => $activeItems,
            'links'      => $activeItems,
            'contact'    => static::defaultContact(),
            'bottom'     => static::defaultBottom(),
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
            'brand'            => static::defaultBrand(),
            'cta'              => is_array($this->cta) ? array_merge(static::defaultCta(), $this->cta) : static::defaultCta(),
            'grid'             => is_array($this->grid) ? $this->grid : static::defaultGrid(),
            'columns'          => $this->buildColumns($allItems),
            'items'            => $allItems,
            'links'            => $allItems,
            'contact'          => static::defaultContact(),
            'bottom'           => static::defaultBottom(),
            'customCode'       => [
                'enabled' => ! empty($custom['enabled']),
                'html'    => (string) ($custom['html'] ?? ''),
                'css'     => (string) ($custom['css'] ?? ''),
                'js'      => (string) ($custom['js'] ?? ''),
            ],
        ];
    }
}
