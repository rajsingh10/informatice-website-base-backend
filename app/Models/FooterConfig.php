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
        'sections',
        'links',
        'customCode',
    ];

    protected $casts = [
        'cta'        => 'array',
        'grid'       => 'array',
        'sections'   => 'array',
        'links'      => 'array',
        'customCode' => 'array',
    ];

    /** Valid section IDs for grid cells */
    public static array $validSections = ['about', 'services', 'quickLinks', 'contact', ''];

    public static array $allowedTemplates = [
        'layout1', // Classic 4-Column Corporate (default)
        'layout2', // Modern Dark Bento with Newsletter
        'layout3', // Centered Brand & Social Flow
        'layout4', // Full-width Enterprise Matrix
    ];

    // ─────────────────────────────────────────────────────────────
    // DEFAULTS
    // ─────────────────────────────────────────────────────────────

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

    public static function defaultSections(): array
    {
        // Default 4 columns × 1 row → 4 cells
        return ['about', 'quickLinks', 'services', 'contact'];
    }

    public static function defaultLinks(): array
    {
        return [
            [
                'id'      => 'footer-home',
                'label'   => 'Home',
                'url'     => '/',
                'enabled' => true,
                'submenu' => [],
            ],
            [
                'id'      => 'footer-about',
                'label'   => 'About',
                'url'     => '/about',
                'enabled' => true,
                'submenu' => [],
            ],
            [
                'id'      => 'footer-services',
                'label'   => 'Services',
                'url'     => '/services',
                'enabled' => true,
                'submenu' => [
                    ['id' => 'footer-services-web',    'label' => 'Web Development',        'url' => '/services/web-development',        'enabled' => true, 'submenu' => []],
                    ['id' => 'footer-services-mobile', 'label' => 'Mobile App Development', 'url' => '/services/mobile-app-development', 'enabled' => true, 'submenu' => []],
                    ['id' => 'footer-services-design', 'label' => 'UI/UX Design',           'url' => '/services/ui-ux-design',           'enabled' => true, 'submenu' => []],
                    ['id' => 'footer-services-cloud',  'label' => 'Cloud Solutions',        'url' => '/services/cloud-solutions',        'enabled' => true, 'submenu' => []],
                ],
            ],
            [
                'id'      => 'footer-portfolio',
                'label'   => 'Portfolio',
                'url'     => '/portfolio',
                'enabled' => true,
                'submenu' => [],
            ],
            [
                'id'      => 'footer-blog',
                'label'   => 'Blog',
                'url'     => '/blog',
                'enabled' => true,
                'submenu' => [],
            ],
            [
                'id'      => 'footer-contact',
                'label'   => 'Contact',
                'url'     => '/contact',
                'enabled' => true,
                'submenu' => [],
            ],
        ];
    }

    /**
     * @deprecated  Use defaultLinks() instead — kept for backward compatibility.
     */
    public static function defaultItems(): array
    {
        return static::defaultLinks();
    }

    public static function defaultBrand(): array
    {
        return [
            'name'    => 'NEXGEN SOLUTIONS',
            'tagline' => 'Transforming Ideas Into Digital Reality',
            'socials' => [
                ['platform' => 'facebook',  'icon' => 'fa-brands fa-facebook-f',    'url' => 'https://facebook.com/nexgen',          'enabled' => true],
                ['platform' => 'twitter',   'icon' => 'fa-brands fa-twitter',       'url' => 'https://twitter.com/nexgen',           'enabled' => true],
                ['platform' => 'instagram', 'icon' => 'fa-brands fa-instagram',     'url' => 'https://instagram.com/nexgen',         'enabled' => true],
                ['platform' => 'linkedin',  'icon' => 'fa-brands fa-linkedin-in',   'url' => 'https://linkedin.com/company/nexgen',  'enabled' => true],
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
            'sections'   => static::defaultSections(),
            'links'      => static::defaultLinks(),
            'customCode' => static::defaultCustomCode(),
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // BACKWARD-COMPAT ACCESSORS (items ↔ links)
    // ─────────────────────────────────────────────────────────────

    public function getItemsAttribute(): array
    {
        return $this->getItemsList();
    }

    public function setItemsAttribute($value): void
    {
        $this->attributes['links'] = is_array($value) ? json_encode($value) : $value;
    }

    // ─────────────────────────────────────────────────────────────
    // SINGLETON
    // ─────────────────────────────────────────────────────────────

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

        $instance = new static();
        $defaults = static::defaults();
        $instance->layoutId   = $defaults['layoutId']   ?? 'layout1';
        $instance->cta        = $defaults['cta']        ?? static::defaultCta();
        $instance->grid       = $defaults['grid']       ?? static::defaultGrid();
        $instance->sections   = $defaults['sections']   ?? static::defaultSections();
        $instance->links      = $defaults['links']      ?? static::defaultLinks();
        $instance->customCode = $defaults['customCode'] ?? static::defaultCustomCode();
        return $instance;
    }

    // ─────────────────────────────────────────────────────────────
    // LINK NORMALIZATION  (supports unlimited submenu depth)
    // ─────────────────────────────────────────────────────────────

    /**
     * Recursively normalize a link or submenu entry.
     * Preserves all user-supplied fields; ensures the required ones exist.
     */
    public static function normalizeLink(array $link, int $idx = 0, string $prefix = 'footer'): array
    {
        $id      = (string) ($link['id'] ?? "{$prefix}-{$idx}");
        $label   = (string) ($link['label'] ?? $link['title'] ?? 'Link');
        $url     = (string) ($link['url']   ?? $link['href']  ?? '/');
        $enabled = isset($link['enabled']) ? (bool) filter_var($link['enabled'], FILTER_VALIDATE_BOOLEAN) : true;

        $rawSubmenu = $link['submenu'] ?? $link['children'] ?? [];
        $submenu = [];
        if (is_array($rawSubmenu)) {
            foreach ($rawSubmenu as $si => $sub) {
                if (is_array($sub)) {
                    $submenu[] = static::normalizeLink($sub, $si, $id);
                }
            }
        }

        return [
            'id'      => $id,
            'label'   => $label,
            'url'     => $url,
            'enabled' => $enabled,
            'submenu' => $submenu,
        ];
    }

    /**
     * Normalize an array of top-level links.
     */
    public static function normalizeLinks(array $raw): array
    {
        $out = [];
        foreach ($raw as $idx => $link) {
            if (is_array($link)) {
                $out[] = static::normalizeLink($link, $idx);
            }
        }
        return $out;
    }

    // ─────────────────────────────────────────────────────────────
    // SECTIONS VALIDATION
    // ─────────────────────────────────────────────────────────────

    /**
     * Validate grid sections array.
     * Returns ['ok' => true] or ['ok' => false, 'error' => '...'].
     */
    public static function validateSections(array $sections, int $columns, int $rows): array
    {
        $expected = $columns * $rows;
        if (count($sections) !== $expected) {
            return [
                'ok'    => false,
                'error' => "sections must have exactly {$expected} entries ({$columns} columns × {$rows} rows), got " . count($sections) . ".",
            ];
        }

        $seen = [];
        foreach ($sections as $i => $cell) {
            // Normalize null → empty string
            if ($cell === null || $cell === false) {
                $cell = '';
            }
            // Cast to string (handles integer 0 etc.)
            $cell = (string) $cell;
            // Replace string "null" or "false" with empty
            if (in_array(strtolower($cell), ['null', 'false', 'none'], true)) {
                $cell = '';
            }
            if (! in_array($cell, static::$validSections, true)) {
                return [
                    'ok'    => false,
                    'error' => "sections[{$i}] value '{$cell}' is not allowed. Valid values: " . implode(', ', array_map(fn ($s) => $s === '' ? '(empty string)' : $s, static::$validSections)) . ".",
                ];
            }
            if ($cell !== '' && in_array($cell, $seen, true)) {
                return [
                    'ok'    => false,
                    'error' => "Section '{$cell}' appears more than once. Each non-empty section may only be assigned to one cell.",
                ];
            }
            if ($cell !== '') {
                $seen[] = $cell;
            }
        }

        return ['ok' => true];
    }

    // ─────────────────────────────────────────────────────────────
    // ITEMS LIST (flat, backward compat)
    // ─────────────────────────────────────────────────────────────

    public function getItemsList(): array
    {
        $raw = is_array($this->links) && ! empty($this->links)
            ? $this->links
            : static::defaultLinks();

        return array_values($raw);
    }

    // ─────────────────────────────────────────────────────────────
    // COLUMNS BUILDER (for legacy public/admin payload)
    // ─────────────────────────────────────────────────────────────

    public function buildColumns(array $items): array
    {
        $quickLinks = [];
        $services   = [];

        foreach ($items as $item) {
            $cat = $item['category'] ?? '';
            $url = $item['url'] ?? '';
            if ($cat === 'services' || str_contains(strtolower($url), '/services/')) {
                $services[] = $item;
            } else {
                $quickLinks[] = $item;
            }
        }

        if (empty($services)) {
            $half       = (int) ceil(count($items) / 2);
            $quickLinks = array_slice($items, 0, $half);
            $services   = array_slice($items, $half);
        }

        $brand   = static::defaultBrand();
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

    // ─────────────────────────────────────────────────────────────
    // PAYLOADS
    // ─────────────────────────────────────────────────────────────

    /**
     * The canonical spec-compliant payload shape:
     *   { links, grid (with sections), cta, customCode }
     * Used internally and returned verbatim by the new endpoints.
     */
    public function toSpecPayload(): array
    {
        $layoutRecord = Layout::find('footerLayout');
        $layoutId     = $layoutRecord?->templateId ?? $this->layoutId ?? 'layout1';

        $links    = is_array($this->links)  && ! empty($this->links)      ? $this->links  : static::defaultLinks();
        $grid     = is_array($this->grid)   && ! empty($this->grid)       ? $this->grid   : static::defaultGrid();
        $sections = is_array($this->sections) && ! empty($this->sections) ? $this->sections : static::defaultSections();
        $cta      = is_array($this->cta)    ? array_merge(static::defaultCta(), $this->cta) : static::defaultCta();
        $custom   = is_array($this->customCode) ? $this->customCode : static::defaultCustomCode();

        return [
            'layoutId' => $layoutId,
            'layout'   => $layoutId,
            'links'    => array_values($links),
            'grid'     => array_merge($grid, ['sections' => array_values($sections)]),
            'cta'      => [
                'enabled'  => (bool) ($cta['enabled']  ?? true),
                'title'    => (string) ($cta['title']    ?? ''),
                'subtitle' => (string) ($cta['subtitle'] ?? ''),
                'label'    => (string) ($cta['label']    ?? 'Schedule Consultation'),
                'url'      => (string) ($cta['url']      ?? '/contact'),
            ],
            'customCode' => [
                'enabled' => (bool) ($custom['enabled'] ?? false),
                'html'    => (string) ($custom['html'] ?? ''),
                'css'     => (string) ($custom['css']  ?? ''),
                'js'      => (string) ($custom['js']   ?? ''),
            ],
        ];
    }

    /**
     * Public payload — also includes legacy columns/items/brand/contact/bottom.
     */
    public function toPublicPayload(): array
    {
        $spec        = $this->toSpecPayload();
        $allItems    = $this->getItemsList();
        $activeItems = array_values(array_filter($allItems, fn ($item) => ! empty($item['enabled'])));

        return array_merge($spec, [
            'enabled' => true,
            'visible' => true,
            'status'  => 'visible',
            'brand'   => static::defaultBrand(),
            'columns' => $this->buildColumns($activeItems),
            'items'   => $activeItems,
            'contact' => static::defaultContact(),
            'bottom'  => static::defaultBottom(),
        ]);
    }

    /**
     * Admin payload — full data including unpublished links + allowed templates.
     */
    public function toAdminPayload(): array
    {
        $spec     = $this->toSpecPayload();
        $allItems = $this->getItemsList();

        return array_merge($spec, [
            'enabled'          => true,
            'visible'          => true,
            'status'           => 'visible',
            'allowedTemplates' => static::$allowedTemplates,
            'brand'            => static::defaultBrand(),
            'columns'          => $this->buildColumns($allItems),
            'items'            => $allItems,
            'contact'          => static::defaultContact(),
            'bottom'           => static::defaultBottom(),
        ]);
    }
}
