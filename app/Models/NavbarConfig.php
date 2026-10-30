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
        'cta' => 'array',
        'items' => 'array',
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
            'label' => 'Get a Quote',
            'url' => '/contact',
        ];
    }

    /**
     * Default configuration map.
     */
    public static function defaults(): array
    {
        return [
            'headerBehavior' => 'fixed',
            'cta' => static::defaultCta(),
            'items' => static::defaultItems(),
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
            $layoutRecord = Layout::find('navbarLayout');
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
        $layoutRecord = Layout::find('navbarLayout');
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
     * Recursively filter items for public display (only enabled items, preserving submenus).
     */
    public static function filterPublicItems(array $items): array
    {
        $result = [];
        foreach ($items as $item) {
            if (! is_array($item) || empty($item['enabled'])) {
                continue;
            }

            $clean = [
                'id' => (string) ($item['id'] ?? ''),
                'label' => (string) ($item['label'] ?? ''),
                'url' => (string) ($item['url'] ?? '/'),
                'enabled' => true,
            ];

            if (isset($item['target'])) {
                $clean['target'] = (string) $item['target'];
            }

            // Recursively filter submenus (submenu / children / subitems / dropdown)
            $children = $item['submenu'] ?? ($item['children'] ?? ($item['subitems'] ?? ($item['dropdown'] ?? null)));
            if (is_array($children) && ! empty($children)) {
                $filteredSubmenu = static::filterPublicItems($children);
                if (! empty($filteredSubmenu)) {
                    $clean['submenu'] = $filteredSubmenu;
                }
            }

            $result[] = $clean;
        }

        return array_values($result);
    }

    /**
     * Format payload for public header (only enabled: true items, recursively).
     */
    public function toPublicPayload(): array
    {
        $items = is_array($this->items) ? $this->items : static::defaultItems();
        $enabledItems = static::filterPublicItems($items);

        $activeBehavior = $this->getBehaviorForLayout();
        $isEnabled = $this->isNavbarEnabled();

        $cleanCta = is_array($this->cta) ? $this->cta : static::defaultCta();
        unset($cleanCta['_behaviors'], $cleanCta['_navbar_enabled']);

        return [
            'enabled' => $isEnabled,
            'visible' => $isEnabled,
            'status' => $isEnabled ? 'visible' : 'hidden',
            'behavior' => $activeBehavior,
            'headerBehavior' => $activeBehavior,
            'cta' => $cleanCta,
            'items' => $enabledItems,
        ];
    }

    /**
     * Format payload for admin header (includes disabled items and all submenu layers).
     */
    public function toAdminPayload(): array
    {
        $activeBehavior = $this->getBehaviorForLayout();
        $isEnabled = $this->isNavbarEnabled();

        $cleanCta = is_array($this->cta) ? $this->cta : static::defaultCta();
        unset($cleanCta['_behaviors'], $cleanCta['_navbar_enabled']);

        return [
            'enabled' => $isEnabled,
            'visible' => $isEnabled,
            'status' => $isEnabled ? 'visible' : 'hidden',
            'behavior' => $activeBehavior,
            'headerBehavior' => $activeBehavior,
            'cta' => $cleanCta,
            'items' => is_array($this->items) ? array_values($this->items) : static::defaultItems(),
        ];
    }

    /**
     * Recursively calculate highest numeric ID in tree.
     */
    public static function getMaxItemId(array $items): int
    {
        $max = 0;
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            if (! empty($item['id']) && preg_match('/^nav-(\d+)$/', (string) $item['id'], $m)) {
                $num = (int) $m[1];
                if ($num > $max) {
                    $max = $num;
                }
            }

            $children = $item['submenu'] ?? ($item['children'] ?? ($item['subitems'] ?? ($item['dropdown'] ?? null)));
            if (is_array($children) && ! empty($children)) {
                $subMax = static::getMaxItemId($children);
                if ($subMax > $max) {
                    $max = $subMax;
                }
            }
        }

        return $max;
    }

    /**
     * Recursively process array of items down to arbitrary depth.
     * Preserves order, assigns ID if missing, normalizes boolean enabled,
     * and supports multiple layers of submenu / children / subitems / dropdown.
     */
    public static function processItems(array $rawItems, ?int &$maxNum = null): array
    {
        if ($maxNum === null) {
            $maxNum = static::getMaxItemId($rawItems);
        }

        $processed = [];
        foreach ($rawItems as $item) {
            if (! is_array($item)) {
                continue;
            }

            $id = ! empty($item['id']) ? (string) $item['id'] : null;

            if (! $id) {
                $maxNum++;
                $id = "nav-{$maxNum}";
            }

            $label = $item['label'] ?? ($item['name'] ?? ($item['title'] ?? 'Link'));
            $url = $item['url'] ?? ($item['path'] ?? ($item['href'] ?? '/'));

            $node = [
                'id' => (string) $id,
                'label' => (string) $label,
                'url' => (string) $url,
                'enabled' => isset($item['enabled']) ? filter_var($item['enabled'], FILTER_VALIDATE_BOOLEAN) : true,
            ];

            if (isset($item['target'])) {
                $node['target'] = (string) $item['target'];
            }

            // Extract children if any
            $children = null;
            if (isset($item['submenu']) && is_array($item['submenu'])) {
                $children = $item['submenu'];
            } elseif (isset($item['children']) && is_array($item['children'])) {
                $children = $item['children'];
            } elseif (isset($item['subitems']) && is_array($item['subitems'])) {
                $children = $item['subitems'];
            } elseif (isset($item['dropdown']) && is_array($item['dropdown'])) {
                $children = $item['dropdown'];
            }

            if ($children !== null) {
                $node['submenu'] = static::processItems($children, $maxNum);
            }

            $processed[] = $node;
        }

        return $processed;
    }

    /**
     * Recursively find item by ID anywhere in tree.
     */
    public static function findItemRecursive(array $items, string $id): ?array
    {
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            if ((string) ($item['id'] ?? '') === (string) $id) {
                return $item;
            }

            if (! empty($item['submenu']) && is_array($item['submenu'])) {
                $found = static::findItemRecursive($item['submenu'], $id);
                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }

    /**
     * Recursively find and modify item by ID anywhere in tree.
     */
    public static function findAndModifyItem(array &$items, string $id, callable $callback): bool
    {
        foreach ($items as $idx => &$item) {
            if (! is_array($item)) {
                continue;
            }

            if ((string) ($item['id'] ?? '') === (string) $id) {
                $item = $callback($item);

                return true;
            }

            if (isset($item['submenu']) && is_array($item['submenu'])) {
                if (static::findAndModifyItem($item['submenu'], $id, $callback)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Recursively find parent item and append new child to its submenu.
     */
    public static function findAndAppendChild(array &$items, string $parentId, array $newItem): bool
    {
        foreach ($items as &$item) {
            if (! is_array($item)) {
                continue;
            }

            if ((string) ($item['id'] ?? '') === (string) $parentId) {
                if (! isset($item['submenu']) || ! is_array($item['submenu'])) {
                    $item['submenu'] = [];
                }
                $item['submenu'][] = $newItem;

                return true;
            }

            if (isset($item['submenu']) && is_array($item['submenu'])) {
                if (static::findAndAppendChild($item['submenu'], $parentId, $newItem)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Recursively find and delete item by ID anywhere in tree.
     */
    public static function findAndDeleteItem(array &$items, string $id, ?array &$deletedItem = null): bool
    {
        foreach ($items as $idx => &$item) {
            if (! is_array($item)) {
                continue;
            }

            if ((string) ($item['id'] ?? '') === (string) $id) {
                $deletedItem = $item;
                array_splice($items, $idx, 1);
                $items = array_values($items);

                return true;
            }

            if (isset($item['submenu']) && is_array($item['submenu'])) {
                if (static::findAndDeleteItem($item['submenu'], $id, $deletedItem)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Recursively move item up or down within its sibling array.
     */
    public static function findAndMoveItem(array &$items, string $id, string $direction = 'up'): bool
    {
        $count = count($items);
        for ($i = 0; $i < $count; $i++) {
            if (! is_array($items[$i])) {
                continue;
            }

            if ((string) ($items[$i]['id'] ?? '') === (string) $id) {
                $target = ($direction === 'down') ? $i + 1 : $i - 1;
                if ($target >= 0 && $target < $count) {
                    $temp = $items[$i];
                    $items[$i] = $items[$target];
                    $items[$target] = $temp;

                    return true;
                }

                return false; // Already at boundary
            }

            if (isset($items[$i]['submenu']) && is_array($items[$i]['submenu'])) {
                if (static::findAndMoveItem($items[$i]['submenu'], $id, $direction)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get next available navigation item ID across the whole tree.
     */
    public function getNextItemId(): string
    {
        $items = is_array($this->items) ? $this->items : static::defaultItems();
        $maxNum = static::getMaxItemId($items);

        return 'nav-'.($maxNum + 1);
    }
}
