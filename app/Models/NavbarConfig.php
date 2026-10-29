<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavbarConfig extends Model
{
    protected $table = 'navbar_configs';

    protected $fillable = [
        'headerBehavior',
        'behaviors',
        'cta',
        'items',
    ];

    protected $casts = [
        'behaviors' => 'array',
        'cta'       => 'array',
        'items'     => 'array',
    ];

    public static array $allowedBehaviors = [
        'fixed',
        'scroll',
        'colored',
        'transparent',
    ];

    /**
     * Default behaviors per navbar layout template.
     * Each layout has a different default behavior.
     */
    public static function defaultBehaviors(): array
    {
        return [
            'layout1' => 'fixed',
            'layout2' => 'transparent',
            'layout3' => 'colored',
            'layout4' => 'scroll',
            'layout5' => 'fixed',
        ];
    }

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
     * Get merged behaviors map for all navbar layout templates.
     */
    public function getBehaviorsMap(): array
    {
        $stored = is_array($this->behaviors) ? $this->behaviors : [];
        return array_merge(static::defaultBehaviors(), $stored);
    }

    /**
     * Get active behavior for a specific navbar layout (or current active navbarLayout).
     */
    public function getBehaviorForLayout(?string $layoutKey = null): string
    {
        if (! $layoutKey) {
            $layoutRecord = \App\Models\Layout::find('navbarLayout');
            $layoutKey = $layoutRecord?->templateId ?? 'layout1';
        }

        $map = $this->getBehaviorsMap();
        return $map[$layoutKey] ?? ($this->headerBehavior ?: 'fixed');
    }

    /**
     * Set behavior for a specific navbar layout template and update active behavior if it matches.
     */
    public function setBehaviorForLayout(string $behavior, ?string $layoutKey = null): void
    {
        $layoutRecord = \App\Models\Layout::find('navbarLayout');
        $currentTemplate = $layoutRecord?->templateId ?? 'layout1';

        $targetLayout = $layoutKey ?: $currentTemplate;

        $map = $this->getBehaviorsMap();
        $map[$targetLayout] = $behavior;
        $this->behaviors = $map;

        if ($targetLayout === $currentTemplate) {
            $this->headerBehavior = $behavior;
        }

        $this->save();
    }

    /**
     * Get navbar enabled/visible status.
     */
    public function isNavbarEnabled(): bool
    {
        if (is_array($this->cta) && isset($this->cta['_navbar_enabled'])) {
            return (bool) $this->cta['_navbar_enabled'];
        }
        return true;
    }

    /**
     * Set navbar enabled/visible status.
     */
    public function setNavbarEnabled(bool $enabled): void
    {
        $cta = is_array($this->cta) ? $this->cta : static::defaultCta();
        $cta['_navbar_enabled'] = $enabled;
        $this->cta = $cta;
        $this->save();
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

        $activeBehavior = $this->getBehaviorForLayout();
        $isEnabled = $this->isNavbarEnabled();

        $cleanCta = is_array($this->cta) ? $this->cta : static::defaultCta();
        unset($cleanCta['_behaviors'], $cleanCta['_navbar_enabled']);

        return [
            'enabled'        => $isEnabled,
            'visible'        => $isEnabled,
            'status'         => $isEnabled ? 'visible' : 'hidden',
            'behavior'       => $activeBehavior,
            'headerBehavior' => $activeBehavior,
            'cta'            => $cleanCta,
            'items'          => $enabledItems,
        ];
    }

    /**
     * Format payload for admin header (includes disabled items).
     */
    public function toAdminPayload(): array
    {
        $activeBehavior = $this->getBehaviorForLayout();
        $isEnabled = $this->isNavbarEnabled();

        $cleanCta = is_array($this->cta) ? $this->cta : static::defaultCta();
        unset($cleanCta['_behaviors'], $cleanCta['_navbar_enabled']);

        return [
            'enabled'        => $isEnabled,
            'visible'        => $isEnabled,
            'status'         => $isEnabled ? 'visible' : 'hidden',
            'behavior'       => $activeBehavior,
            'headerBehavior' => $activeBehavior,
            'cta'            => $cleanCta,
            'items'          => is_array($this->items) ? array_values($this->items) : static::defaultItems(),
        ];
    }

    /**
     * Process array of items: assign ID if missing, preserve order, normalize boolean enabled.
     * Supports label/name/title and url/path/href.
     */
    public static function processItems(array $rawItems): array
    {
        $existingIds = [];
        $maxNum = 0;

        foreach ($rawItems as $item) {
            if (! empty($item['id']) && preg_match('/^nav-(\d+)$/', (string) $item['id'], $m)) {
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

            $label = $item['label'] ?? ($item['name'] ?? ($item['title'] ?? 'Link'));
            $url   = $item['url'] ?? ($item['path'] ?? ($item['href'] ?? '/'));

            $processed[] = [
                'id'      => (string) $id,
                'label'   => (string) $label,
                'url'     => (string) $url,
                'enabled' => isset($item['enabled']) ? filter_var($item['enabled'], FILTER_VALIDATE_BOOLEAN) : true,
            ];
        }

        return $processed;
    }

    /**
     * Get next available navigation item ID.
     */
    public function getNextItemId(): string
    {
        $items = is_array($this->items) ? $this->items : static::defaultItems();
        $maxNum = 0;
        foreach ($items as $item) {
            if (! empty($item['id']) && preg_match('/^nav-(\d+)$/', (string) $item['id'], $m)) {
                $num = (int) $m[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }
        return 'nav-' . ($maxNum + 1);
    }
}

