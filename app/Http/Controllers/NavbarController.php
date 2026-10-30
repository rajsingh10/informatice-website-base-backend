<?php

namespace App\Http\Controllers;

use App\Models\Layout;
use App\Models\NavbarConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NavbarController extends Controller
{
    /**
     * GET /public/navigation/navbar — No Auth
     * Returns everything public header needs (only enabled: true items, no templateId).
     */
    public function showPublic(): JsonResponse
    {
        $config = NavbarConfig::getSingleton();

        return response()->json([
            'success' => true,
            'data' => $config->toPublicPayload(),
        ], 200);
    }

    /**
     * GET /admin/navigation/navbar — Auth Required
     * Admin config (includes disabled items).
     */
    public function showAdmin(): JsonResponse
    {
        $config = NavbarConfig::getSingleton();

        return response()->json([
            'success' => true,
            'data' => $config->toAdminPayload(),
        ], 200);
    }

    /**
     * PUT/POST /public/navigation/navbar & /admin/navigation/navbar
     * General Navbar update: supports behavior, cta, items, and navbarLayout.
     */
    public function updateNavbar(Request $request): JsonResponse
    {
        $config = NavbarConfig::getSingleton();

        // 1. Behavior update
        $behavior = $request->input('behavior', $request->input('headerBehavior'));
        $targetLayout = $request->input('layout', $request->input('navbarLayout'));
        if ($behavior !== null) {
            $request->validate([
                'behavior' => [
                    'sometimes',
                    'string',
                    Rule::in(NavbarConfig::$allowedBehaviors),
                ],
                'headerBehavior' => [
                    'sometimes',
                    'string',
                    Rule::in(NavbarConfig::$allowedBehaviors),
                ],
            ], [
                'behavior.in' => 'Behavior must be one of: '.implode(', ', NavbarConfig::$allowedBehaviors),
                'headerBehavior.in' => 'headerBehavior must be one of: '.implode(', ', NavbarConfig::$allowedBehaviors),
            ]);

            $config->setBehaviorForLayout($behavior, $targetLayout);
        }

        // Support passing multiple layout behaviors at once: { "behaviors": { "layout1": "fixed", "layout2": "transparent", ... } }
        if ($request->has('behaviors') && is_array($request->input('behaviors'))) {
            foreach ($request->input('behaviors') as $lKey => $bVal) {
                if (in_array($bVal, NavbarConfig::$allowedBehaviors)) {
                    $config->setBehaviorForLayout($bVal, $lKey);
                }
            }
        }

        // 2. CTA Button update
        if ($request->has('cta')) {
            $ctaInput = $request->input('cta');
            if (is_array($ctaInput)) {
                $config->cta = [
                    'enabled' => isset($ctaInput['enabled']) ? (bool) $ctaInput['enabled'] : true,
                    'label' => (string) ($ctaInput['label'] ?? 'Get a Quote'),
                    'url' => (string) ($ctaInput['url'] ?? '/contact'),
                ];
            }
        }

        // 3. Menu Items update
        if ($request->has('items')) {
            $itemsInput = $request->input('items');
            if (is_array($itemsInput)) {
                $config->items = NavbarConfig::processItems($itemsInput);
            }
        }

        // 4. Optional Layout update (navbarLayout or layout)
        $layoutId = $request->input('navbarLayout', $request->input('layout'));
        if ($layoutId !== null) {
            $allowed = Layout::allowedFor('navbarLayout');
            if (in_array($layoutId, $allowed)) {
                Layout::updateOrCreate(
                    ['key' => 'navbarLayout'],
                    ['templateId' => $layoutId, 'updatedAt' => now()]
                );
                // Also sync active behavior if not explicitly overridden
                if ($behavior === null) {
                    $config->headerBehavior = $config->getBehaviorForLayout($layoutId);
                }
            }
        }

        $config->save();

        return response()->json([
            'success' => true,
            'message' => 'Navbar configuration updated successfully.',
            'data' => $config->fresh()->toPublicPayload(),
        ], 200);
    }

    /**
     * PUT /admin/navigation/navbar/behavior — Auth Required
     * Set scroll behavior: fixed, scroll, colored, transparent.
     */
    public function updateBehavior(Request $request): JsonResponse
    {
        $request->validate([
            'behavior' => [
                'required',
                'string',
                Rule::in(NavbarConfig::$allowedBehaviors),
            ],
            'layout' => [
                'sometimes',
                'string',
                Rule::in(Layout::allowedFor('navbarLayout')),
            ],
        ], [
            'behavior.in' => 'Behavior must be one of: '.implode(', ', NavbarConfig::$allowedBehaviors),
        ]);

        $behavior = $request->input('behavior');
        $targetLayout = $request->input('layout');

        $config = NavbarConfig::getSingleton();
        $config->setBehaviorForLayout($behavior, $targetLayout);

        return response()->json([
            'success' => true,
            'message' => "Navbar behavior updated to {$behavior}.",
            'data' => [
                'behavior' => $behavior,
                'headerBehavior' => $behavior,
                'behaviors' => $config->getBehaviorsMap(),
                'navbarBehaviors' => $config->getBehaviorsMap(),
            ],
        ], 200);
    }

    /**
     * GET /admin/navigation/navbar/items & /admin/navbar/items — Auth Required
     * List all navbar menu items.
     */
    public function getItems(Request $request): JsonResponse
    {
        $config = NavbarConfig::getSingleton();
        $items = is_array($config->items) ? array_values($config->items) : NavbarConfig::defaultItems();

        return response()->json([
            'success' => true,
            'count' => count($items),
            'items' => $items,
            'data' => $items,
        ], 200);
    }

    /**
     * POST /admin/navigation/navbar/items & /admin/navbar/items — Auth Required
     * Add a single new menu link (at root or as a sub-item if parentId provided),
     * or bulk update if 'items' array provided.
     */
    public function addItem(Request $request): JsonResponse
    {
        // If client passed full items array, perform bulk update
        if ($request->has('items') && is_array($request->input('items'))) {
            return $this->updateItems($request);
        }

        $label = $request->input('label', $request->input('name', $request->input('title')));
        $url = $request->input('url', $request->input('path', $request->input('href')));

        if (empty($label)) {
            return response()->json([
                'success' => false,
                'message' => 'The link name (label) field is required.',
                'errors' => ['label' => ['The link name/label field is required.']],
            ], 422);
        }

        if (empty($url)) {
            return response()->json([
                'success' => false,
                'message' => 'The URL path field is required.',
                'errors' => ['url' => ['The URL path field is required.']],
            ], 422);
        }

        $enabled = $request->has('enabled')
            ? filter_var($request->input('enabled'), FILTER_VALIDATE_BOOLEAN)
            : true;

        $config = NavbarConfig::getSingleton();
        $items = is_array($config->items) ? array_values($config->items) : NavbarConfig::defaultItems();

        $newItem = [
            'id' => $config->getNextItemId(),
            'label' => (string) $label,
            'url' => (string) $url,
            'enabled' => $enabled,
        ];

        if ($request->has('target')) {
            $newItem['target'] = (string) $request->input('target');
        }

        // Support nested submenu passed during item creation
        if ($request->has('submenu') && is_array($request->input('submenu'))) {
            $newItem['submenu'] = NavbarConfig::processItems($request->input('submenu'));
        } elseif ($request->has('children') && is_array($request->input('children'))) {
            $newItem['submenu'] = NavbarConfig::processItems($request->input('children'));
        }

        $parentId = $request->input('parentId', $request->input('parent_id'));

        if (! empty($parentId) && $parentId !== 'root' && $parentId !== '0') {
            $attached = NavbarConfig::findAndAppendChild($items, (string) $parentId, $newItem);

            if (! $attached) {
                return response()->json([
                    'success' => false,
                    'message' => "Parent menu item with ID '{$parentId}' not found.",
                ], 404);
            }

            $config->update(['items' => $items]);

            return response()->json([
                'success' => true,
                'message' => "Submenu link '{$newItem['label']}' added under parent '{$parentId}' successfully.",
                'parentId' => $parentId,
                'item' => $newItem,
                'items' => $items,
                'data' => $items,
            ], 201);
        }

        $items[] = $newItem;
        $config->update(['items' => $items]);

        return response()->json([
            'success' => true,
            'message' => "Navbar menu link '{$newItem['label']}' added successfully.",
            'item' => $newItem,
            'items' => $items,
            'data' => $items,
        ], 201);
    }

    /**
     * POST /admin/navigation/navbar/items/{parentId}/subitems — Auth Required
     * Add a child submenu item directly under specified parent item ID.
     */
    public function addSubItem(Request $request, string $parentId): JsonResponse
    {
        $request->merge(['parentId' => $parentId]);

        return $this->addItem($request);
    }

    /**
     * PUT|POST /admin/navigation/navbar/items/{id} — Auth Required
     * Edit / update an existing menu link (label, url, enabled/visibility, submenu) at any nesting depth.
     */
    public function updateItem(Request $request, string $id): JsonResponse
    {
        $config = NavbarConfig::getSingleton();
        $items = is_array($config->items) ? array_values($config->items) : NavbarConfig::defaultItems();

        $updatedItem = null;
        $found = NavbarConfig::findAndModifyItem($items, (string) $id, function ($item) use ($request, &$updatedItem) {
            if ($request->has('label') || $request->has('name') || $request->has('title')) {
                $item['label'] = (string) $request->input('label', $request->input('name', $request->input('title')));
            }

            if ($request->has('url') || $request->has('path') || $request->has('href')) {
                $item['url'] = (string) $request->input('url', $request->input('path', $request->input('href')));
            }

            if ($request->has('enabled')) {
                $item['enabled'] = filter_var($request->input('enabled'), FILTER_VALIDATE_BOOLEAN);
            }

            if ($request->has('target')) {
                $item['target'] = (string) $request->input('target');
            }

            // Update submenu if provided
            if ($request->has('submenu') && is_array($request->input('submenu'))) {
                $item['submenu'] = NavbarConfig::processItems($request->input('submenu'));
            } elseif ($request->has('children') && is_array($request->input('children'))) {
                $item['submenu'] = NavbarConfig::processItems($request->input('children'));
            }

            $updatedItem = $item;

            return $item;
        });

        if (! $found) {
            return response()->json([
                'success' => false,
                'message' => "Navbar menu link with ID '{$id}' not found.",
            ], 404);
        }

        $config->update(['items' => $items]);

        return response()->json([
            'success' => true,
            'message' => "Navbar menu link '{$updatedItem['label']}' updated successfully.",
            'item' => $updatedItem,
            'items' => $items,
            'data' => $items,
        ], 200);
    }

    /**
     * POST|PUT /admin/navigation/navbar/items/{id}/toggle — Auth Required
     * Toggle visibility (show/hide) of a menu link anywhere in the hierarchy.
     */
    public function toggleItem(Request $request, string $id): JsonResponse
    {
        $config = NavbarConfig::getSingleton();
        $items = is_array($config->items) ? array_values($config->items) : NavbarConfig::defaultItems();

        $toggledItem = null;
        $found = NavbarConfig::findAndModifyItem($items, (string) $id, function ($item) use (&$toggledItem) {
            $item['enabled'] = ! ($item['enabled'] ?? true);
            $toggledItem = $item;

            return $item;
        });

        if (! $found) {
            return response()->json([
                'success' => false,
                'message' => "Navbar menu link with ID '{$id}' not found.",
            ], 404);
        }

        $config->update(['items' => $items]);

        $status = $toggledItem['enabled'] ? 'visible' : 'hidden';

        return response()->json([
            'success' => true,
            'message' => "Navbar menu link '{$toggledItem['label']}' is now {$status}.",
            'item' => $toggledItem,
            'items' => $items,
            'data' => $items,
        ], 200);
    }

    /**
     * DELETE|POST /admin/navigation/navbar/items/{id} — Auth Required
     * Delete / remove a menu link from header navigation anywhere in the hierarchy.
     */
    public function deleteItem(Request $request, string $id): JsonResponse
    {
        $config = NavbarConfig::getSingleton();
        $items = is_array($config->items) ? array_values($config->items) : NavbarConfig::defaultItems();

        $deletedItem = null;
        $found = NavbarConfig::findAndDeleteItem($items, (string) $id, $deletedItem);

        if (! $found) {
            return response()->json([
                'success' => false,
                'message' => "Navbar menu link with ID '{$id}' not found.",
            ], 404);
        }

        $config->update(['items' => $items]);

        return response()->json([
            'success' => true,
            'message' => "Navbar menu link '{$deletedItem['label']}' deleted successfully.",
            'deletedId' => $id,
            'item' => $deletedItem,
            'items' => $items,
            'data' => $items,
        ], 200);
    }

    /**
     * PUT|POST /admin/navigation/navbar/items/reorder — Auth Required
     * Reorder menu links by list of IDs or array of items (at root or within a parent's submenu).
     */
    public function reorderItems(Request $request): JsonResponse
    {
        $config = NavbarConfig::getSingleton();
        $items = is_array($config->items) ? array_values($config->items) : NavbarConfig::defaultItems();

        $parentId = $request->input('parentId', $request->input('parent_id'));
        $itemIds = $request->input('itemIds', $request->input('order', []));

        if (! empty($parentId) && ! empty($itemIds) && is_array($itemIds)) {
            $reordered = false;
            NavbarConfig::findAndModifyItem($items, (string) $parentId, function ($item) use ($itemIds, &$reordered) {
                if (isset($item['submenu']) && is_array($item['submenu'])) {
                    $byKey = [];
                    foreach ($item['submenu'] as $sub) {
                        if (! empty($sub['id'])) {
                            $byKey[$sub['id']] = $sub;
                        }
                    }
                    $newSub = [];
                    foreach ($itemIds as $sId) {
                        if (isset($byKey[$sId])) {
                            $newSub[] = $byKey[$sId];
                            unset($byKey[$sId]);
                        }
                    }
                    foreach ($byKey as $rem) {
                        $newSub[] = $rem;
                    }
                    $item['submenu'] = $newSub;
                    $reordered = true;
                }

                return $item;
            });

            if ($reordered) {
                $config->update(['items' => $items]);
            }
        } elseif (! empty($itemIds) && is_array($itemIds)) {
            $itemsById = [];
            foreach ($items as $item) {
                if (! empty($item['id'])) {
                    $itemsById[$item['id']] = $item;
                }
            }

            $reordered = [];
            foreach ($itemIds as $id) {
                if (isset($itemsById[$id])) {
                    $reordered[] = $itemsById[$id];
                    unset($itemsById[$id]);
                }
            }

            foreach ($itemsById as $item) {
                $reordered[] = $item;
            }

            $items = $reordered;
            $config->update(['items' => $items]);
        } elseif ($request->has('items') && is_array($request->input('items'))) {
            return $this->updateItems($request);
        }

        return response()->json([
            'success' => true,
            'message' => 'Navbar menu links reordered successfully.',
            'items' => $items,
            'data' => $items,
        ], 200);
    }

    /**
     * POST|PUT /admin/navigation/navbar/items/{id}/move-up & /move-down — Auth Required
     * Shift menu link order position up or down by 1 within its sibling list.
     */
    public function moveItem(Request $request, string $id, string $direction = 'up'): JsonResponse
    {
        $config = NavbarConfig::getSingleton();
        $items = is_array($config->items) ? array_values($config->items) : NavbarConfig::defaultItems();

        $moved = NavbarConfig::findAndMoveItem($items, (string) $id, $direction);

        if (! $moved) {
            $exists = NavbarConfig::findItemRecursive($items, (string) $id);
            if (! $exists) {
                return response()->json([
                    'success' => false,
                    'message' => "Navbar menu link with ID '{$id}' not found.",
                ], 404);
            }
        }

        $config->update(['items' => $items]);

        return response()->json([
            'success' => true,
            'message' => "Navbar menu link moved {$direction}.",
            'items' => $items,
            'data' => $items,
        ], 200);
    }

    /**
     * POST|PUT /admin/navigation/navbar/items/{id}/move-up
     */
    public function moveUp(Request $request, string $id): JsonResponse
    {
        return $this->moveItem($request, $id, 'up');
    }

    /**
     * POST|PUT /admin/navigation/navbar/items/{id}/move-down
     */
    public function moveDown(Request $request, string $id): JsonResponse
    {
        return $this->moveItem($request, $id, 'down');
    }

    /**
     * PUT|POST /admin/navigation/navbar/items — Auth Required
     * Replace the full menu array. Server preserves array position order,
     * assigns ID to new items, deletes missing items.
     */
    public function updateItems(Request $request): JsonResponse
    {
        // If single item fields provided instead of items array, redirect to addItem
        if (! $request->has('items') && ($request->filled('label') || $request->filled('name'))) {
            return $this->addItem($request);
        }

        $rawItems = $request->input('items', []);
        if (! is_array($rawItems)) {
            return response()->json([
                'success' => false,
                'message' => 'The items field must be an array of menu links.',
            ], 422);
        }

        $processed = NavbarConfig::processItems($rawItems);

        $config = NavbarConfig::getSingleton();
        $config->update(['items' => $processed]);

        return response()->json([
            'success' => true,
            'message' => 'Navbar menu items updated successfully.',
            'items' => $processed,
            'data' => [
                'items' => $processed,
            ],
        ], 200);
    }

    /**
     * PUT /admin/navigation/navbar/cta — Auth Required
     * Update CTA button (enabled, label, url).
     */
    public function updateCta(Request $request): JsonResponse
    {
        $request->validate([
            'enabled' => 'required|boolean',
            'label' => 'required|string|max:100',
            'url' => 'required|string|max:255',
        ]);

        $cta = [
            'enabled' => (bool) $request->input('enabled'),
            'label' => (string) $request->input('label'),
            'url' => (string) $request->input('url'),
        ];

        $config = NavbarConfig::getSingleton();
        $config->update(['cta' => $cta]);

        return response()->json([
            'success' => true,
            'message' => 'Navbar CTA button updated successfully.',
            'data' => [
                'cta' => $cta,
            ],
        ], 200);
    }

    /**
     * POST /admin/navigation/navbar/reset — Auth Required
     * Reset all navbar configuration to defaults.
     */
    public function reset(): JsonResponse
    {
        $config = NavbarConfig::getSingleton();
        $config->update(NavbarConfig::defaults());

        return response()->json([
            'success' => true,
            'message' => 'Navbar configuration reset to defaults.',
            'data' => $config->fresh()->toAdminPayload(),
        ], 200);
    }
}
