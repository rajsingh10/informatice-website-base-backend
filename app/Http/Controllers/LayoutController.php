<?php

namespace App\Http\Controllers;

use App\Models\Layout;
use App\Models\NavbarConfig;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LayoutController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────
    // GET /public/layouts & /api/public/layouts — No Auth
    // Returns all active layouts in one object with navbarLayout as { layout, behavior }.
    // ──────────────────────────────────────────────────────────────────────
    public function index(): JsonResponse
    {
        $dbLayouts = Layout::all()->pluck('templateId', 'key')->toArray();
        $layouts = [];
        foreach (Layout::validKeys() as $k) {
            $layouts[$k] = $dbLayouts[$k] ?? 'layout1';
        }
        $navbar = NavbarConfig::getSingleton();
        $ordered = [];

        // 1. topbarLayout (FIRST)
        $currentTopbarTemplate = $layouts['topbarLayout'] ?? 'layout1';
        $isTopbarEnabled = TopbarController::isTopbarEnabled();
        $ordered['topbarLayout'] = [
            'layout'   => $isTopbarEnabled ? ($currentTopbarTemplate === 'layout0' ? 'layout1' : $currentTopbarTemplate) : 'layout0',
            'enabled'  => $isTopbarEnabled,
            'visible'  => $isTopbarEnabled,
            'status'   => $isTopbarEnabled ? 'visible' : 'hidden',
        ];

        // 2. navbarLayout (SECOND)
        $currentNavbarTemplate = $layouts['navbarLayout'] ?? 'layout1';
        $activeBehavior = $navbar->getBehaviorForLayout($currentNavbarTemplate);
        $isEnabled = $navbar->isNavbarEnabled();
        $navItems = is_array($navbar->items) ? array_values($navbar->items) : NavbarConfig::defaultItems();
        $ordered['navbarLayout'] = [
            'layout'   => $currentNavbarTemplate,
            'behavior' => $activeBehavior,
            'enabled' => $isEnabled,
            'visible' => $isEnabled,
            'status' => $isEnabled ? 'visible' : 'hidden',
            'items' => $navItems,
        ];

        $currentTopbarTemplate = $layouts['topbarLayout'] ?? 'layout1';
        $isTopbarEnabled = TopbarController::isTopbarEnabled();
        $layouts['topbarLayout'] = [
            'layout' => $isTopbarEnabled ? ($currentTopbarTemplate === 'layout0' ? 'layout1' : $currentTopbarTemplate) : 'layout0',
            'enabled' => $isTopbarEnabled,
            'visible' => $isTopbarEnabled,
            'status' => $isTopbarEnabled ? 'visible' : 'hidden',
        ];
        // 3. Page layouts in clean architectural order
        foreach (Layout::$modules as $modKey => $modMeta) {
            if ($modKey === 'topbarLayout' || $modKey === 'navbarLayout' || $modKey === 'footerLayout') {
                continue;
            }
            $ordered[$modKey] = $layouts[$modKey] ?? 'layout1';
        }

        // Preserve any custom or database-stored layout keys
        foreach ($layouts as $dbKey => $dbVal) {
            if (! isset($ordered[$dbKey]) && $dbKey !== 'footerLayout') {
                $ordered[$dbKey] = $dbVal;
            }
        }

        // 4. footerLayout (LAST)
        $footer = \App\Models\FooterConfig::getSingleton();
        $currentFooterTemplate = $layouts['footerLayout'] ?? ($footer->layoutId ?? 'layout1');
        $footerPayload = $footer->toPublicPayload();
        $footerPayload['layout'] = $currentFooterTemplate;
        $footerPayload['layoutId'] = $currentFooterTemplate;
        $ordered['footerLayout'] = $footerPayload;

        return response()->json([
            'success' => true,
            'data'    => $ordered,
        ], 200);
    }

    // ──────────────────────────────────────────────────────────────────────
    // GET /admin/layouts & /api/admin/layouts — Auth Required
    // Returns detailed layout templates & allowed options for all modules.
    // ──────────────────────────────────────────────────────────────────────
    public function indexAdmin(): JsonResponse
    {
        $records = Layout::all()->keyBy('key');
        $navbar = NavbarConfig::getSingleton();
        $modules = [];
        $active = [];

        foreach (Layout::$modules as $key => $meta) {
            $current = $records->get($key)?->templateId ?? 'layout1';

            if ($key === 'navbarLayout') {
                $isEnabled = $navbar->isNavbarEnabled();
                $navItems = is_array($navbar->items) ? array_values($navbar->items) : NavbarConfig::defaultItems();
                $active[$key] = [
                    'layout' => $current,
                    'behavior' => $navbar->getBehaviorForLayout($current),
                    'enabled' => $isEnabled,
                    'visible' => $isEnabled,
                    'status' => $isEnabled ? 'visible' : 'hidden',
                    'items' => $navItems,
                ];
            } elseif ($key === 'topbarLayout') {
                $isTopbarEnabled = TopbarController::isTopbarEnabled();
                $active[$key] = [
                    'layout' => $isTopbarEnabled ? ($current === 'layout0' ? 'layout1' : $current) : 'layout0',
                    'enabled' => $isTopbarEnabled,
                    'visible' => $isTopbarEnabled,
                    'status' => $isTopbarEnabled ? 'visible' : 'hidden',
                ];
            } elseif ($key === 'footerLayout') {
                $footer = \App\Models\FooterConfig::getSingleton();
                $adminPayload = $footer->toAdminPayload();
                $adminPayload['layout'] = $current;
                $adminPayload['layoutId'] = $current;
                $active[$key] = $adminPayload;
            } else {
                $active[$key] = $current;
            }

            $modules[] = [
                'key' => $key,
                'name' => $meta['name'],
                'templateId' => $current,
                'allowed' => Layout::allowedFor($key),
                'updatedAt' => $records->get($key)?->updatedAt,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'active' => $active,
                'modules' => $modules,
            ],
        ], 200);
    }

    /**
     * POST|PUT /admin/layouts & /api/admin/layouts — Auth Required
     * Accepts:
     * { "layout": "layout5", "behavior": "scroll" }
     * OR { "key": "navbarLayout", "layout": "layout5", "behavior": "scroll" }
     * OR batch layouts.
     */
    public function store(Request $request): JsonResponse
    {
        if ($request->has('layout') || $request->has('templateId')) {
            $key = $request->input('key', 'navbarLayout');

            return $this->update($request, $key);
        }

        return $this->save($request);
    }

    // ──────────────────────────────────────────────────────────────────────
    // GET /admin/layouts/:key & /api/admin/layouts/:key & /public/layouts/:key
    // Show one layout's configuration.
    // ──────────────────────────────────────────────────────────────────────
    public function show(string $key): JsonResponse
    {
        if (! in_array($key, Layout::validKeys())) {
            return response()->json([
                'success' => false,
                'message' => "Invalid layout key '{$key}'.",
                'errors'  => [
                    'key' => ['The key must be one of: ' . implode(', ', Layout::validKeys()) . '.'],
                ],
            ], 422);
        }

        $record = Layout::find($key);
        $current = $record?->templateId ?? 'layout1';
        $allowed = Layout::allowedFor($key);

        if ($key === 'navbarLayout') {
            $navbar = NavbarConfig::getSingleton();
            $data = [
                'key'        => $key,
                'layout'     => $current,
                'templateId' => $current,
                'behavior'   => $navbar->getBehaviorForLayout($current),
                'enabled'    => $navbar->isNavbarEnabled(),
                'visible'    => $navbar->isNavbarEnabled(),
                'status'     => $navbar->isNavbarEnabled() ? 'visible' : 'hidden',
                'items'      => is_array($navbar->items) ? array_values($navbar->items) : NavbarConfig::defaultItems(),
                'allowed'    => $allowed,
            ];
        } elseif ($key === 'topbarLayout') {
            $isTopEnabled = TopbarController::isTopbarEnabled();
            $data = [
                'key'        => $key,
                'layout'     => $isTopEnabled ? ($current === 'layout0' ? 'layout1' : $current) : 'layout0',
                'templateId' => $current,
                'enabled'    => $isTopEnabled,
                'visible'    => $isTopEnabled,
                'status'     => $isTopEnabled ? 'visible' : 'hidden',
                'allowed'    => $allowed,
            ];
        } elseif ($key === 'footerLayout') {
            $footer = \App\Models\FooterConfig::getSingleton();
            $footItems = $footer->getItemsList();
            $data = [
                'key'        => $key,
                'layout'     => $current,
                'templateId' => $current,
                'enabled'    => true,
                'visible'    => true,
                'status'     => 'visible',
                'items'      => $footItems,
                'links'      => $footItems,
                'grid'       => is_array($footer->grid) ? $footer->grid : \App\Models\FooterConfig::defaultGrid(),
                'cta'        => is_array($footer->cta) ? $footer->cta : \App\Models\FooterConfig::defaultCta(),
                'customCode' => is_array($footer->customCode) ? $footer->customCode : \App\Models\FooterConfig::defaultCustomCode(),
                'allowed'    => $allowed,
            ];
        } else {
            $data = [
                'key'        => $key,
                'layout'     => $current,
                'templateId' => $current,
                'allowed'    => $allowed,
            ];
        }

        return response()->json([
            'success' => true,
            'data'    => $data,
        ], 200);
    }

    // ──────────────────────────────────────────────────────────────────────
    // PUT /admin/layouts/:key & /api/admin/layouts/:key — Auth Required
    // Set active template for one key or behavior.
    // ──────────────────────────────────────────────────────────────────────
    public function update(Request $request, string $key): JsonResponse
    {
        // Special case: update scroll behavior via layout key
        if ($key === 'behavior' || $key === 'navbarBehavior') {
            $val = $request->input('behavior', $request->input('templateId', $request->input('value')));
            $targetLayout = $request->input('layout');
            if (! in_array($val, NavbarConfig::$allowedBehaviors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Behavior must be one of: '.implode(', ', NavbarConfig::$allowedBehaviors),
                    'errors' => ['behavior' => ['Allowed: fixed, scroll, colored, transparent']],
                ], 422);
            }
            $navbar = NavbarConfig::getSingleton();
            $navbar->setBehaviorForLayout($val, $targetLayout);

            return response()->json([
                'success' => true,
                'message' => "Navbar behavior updated to {$val}",
                'data' => [
                    'layout' => $targetLayout ?: ($navbar->getBehaviorForLayout() ?? 'layout1'),
                    'behavior' => $val,
                    'navbarBehavior' => $val,
                    'navbarBehaviors' => $navbar->getBehaviorsMap(),
                ],
            ], 200);
        }

        // 1. :key must be in the specification table, otherwise 422
        if (! in_array($key, Layout::validKeys())) {
            return response()->json([
                'success' => false,
                'message' => "Invalid layout key '{$key}'.",
                'errors' => [
                    'key' => [
                        'The key must be one of: '.implode(', ', Layout::validKeys()).'.',
                    ],
                ],
            ], 422);
        }

        // 2. templateId or layout must be within that key's allowed range
        $allowed = Layout::allowedFor($key);
        $templateId = $request->input('layout', $request->input('templateId'));

        // [Contact Page API & Layouts] Normalize numeric layout (e.g. 1 -> layout1)
        if (is_numeric($templateId) && in_array("layout{$templateId}", $allowed, true)) {
            $templateId = "layout{$templateId}";
        }

        if (! $templateId || ! in_array($templateId, $allowed)) {
            return response()->json([
                'success' => false,
                'message' => 'The layout/templateId must be within the allowed range ('.implode(', ', $allowed).').',
                'errors' => [
                    'layout' => ['The layout/templateId field must be one of: '.implode(', ', $allowed).'.'],
                ],
            ], 422);
        }

        // 3. Update or insert the layout key
        Layout::updateOrCreate(
            ['key' => $key],
            [
                'templateId' => $templateId,
                'updatedAt' => now(),
            ]
        );

        // 4. If navbarLayout was updated, sync active behavior and enabled status
        $navbar = NavbarConfig::getSingleton();
        if ($key === 'navbarLayout') {
            if ($request->has('behavior')) {
                $customBehavior = $request->input('behavior');
                if (in_array($customBehavior, NavbarConfig::$allowedBehaviors)) {
                    $navbar->setBehaviorForLayout($customBehavior, $templateId);
                }
            } else {
                $targetBehavior = $navbar->getBehaviorForLayout($templateId);
                $navbar->update(['headerBehavior' => $targetBehavior]);
            }

            if ($request->has('enabled')) {
                $navbar->setNavbarEnabled((bool) $request->input('enabled'));
            } elseif ($request->has('visible')) {
                $navbar->setNavbarEnabled((bool) $request->input('visible'));
            } elseif ($request->has('status')) {
                $st = $request->input('status');
                $navbar->setNavbarEnabled(in_array($st, ['visible', 'on', 'true', '1', 1], true));
            }

            if ($request->has('items') && is_array($request->input('items'))) {
                $processed = NavbarConfig::processItems($request->input('items'));
                $navbar->update(['items' => $processed]);
            } elseif ($request->has('menu') && is_array($request->input('menu'))) {
                $processed = NavbarConfig::processItems($request->input('menu'));
                $navbar->update(['items' => $processed]);
            }
        } elseif ($key === 'topbarLayout') {
            $setting = SiteSetting::getSingleton();
            $ann = is_array($setting->announcement) ? $setting->announcement : [];
            if (! isset($ann['_topbar'])) {
                $ann['_topbar'] = [];
            }

            if ($request->has('enabled') || $request->has('visible') || $request->has('status')) {
                $isTopEnabled = true;
                if ($request->has('enabled')) {
                    $isTopEnabled = filter_var($request->input('enabled'), FILTER_VALIDATE_BOOLEAN);
                } elseif ($request->has('visible')) {
                    $isTopEnabled = filter_var($request->input('visible'), FILTER_VALIDATE_BOOLEAN);
                } elseif ($request->has('status')) {
                    $st = (string) $request->input('status');
                    $isTopEnabled = in_array(strtolower($st), ['visible', 'on', 'true', '1'], true);
                }
                $ann['_topbar']['enabled'] = $isTopEnabled;
            }

            if ($templateId === 'layout0') {
                $ann['_topbar']['enabled'] = false;
            } elseif ($templateId !== 'layout0' && ! ($ann['_topbar']['enabled'] ?? true)) {
                $ann['_topbar']['enabled'] = true;
            }

            if ($request->has('email')) {
                $setting->email = (string) $request->input('email');
            }
            if ($request->has('phone')) {
                $setting->phone = (string) $request->input('phone');
            }
            if ($request->has('cta')) {
                $ctaIn = $request->input('cta');
                if (is_array($ctaIn)) {
                    $ann['cta'] = [
                        'enabled' => isset($ctaIn['enabled']) ? filter_var($ctaIn['enabled'], FILTER_VALIDATE_BOOLEAN) : true,
                        'label' => (string) ($ctaIn['label'] ?? 'Get a Quote'),
                        'url' => (string) ($ctaIn['url'] ?? '/contact'),
                    ];
                }
            }

            $setting->announcement = $ann;
            $setting->save();
        } elseif ($key === 'footerLayout') {
            $footer = \App\Models\FooterConfig::getSingleton();
            $footer->layoutId = $templateId;

            if ($request->has('items') || $request->has('links')) {
                $fIn = $request->input('items', $request->input('links'));
                if (is_array($fIn)) {
                    $footer->items = $fIn;
                    $footer->links = $fIn;
                }
            }
            if ($request->has('cta') && is_array($request->input('cta'))) {
                $footer->cta = $request->input('cta');
            }
            if ($request->has('grid') && is_array($request->input('grid'))) {
                $footer->grid = $request->input('grid');
            }
            $footer->save();
        }

        // 5. Message names the module and the new template
        $moduleName = Layout::moduleName($key);
        $activeBehavior = $navbar->getBehaviorForLayout($templateId);
        $isEnabled = $navbar->isNavbarEnabled();
        $navItems = is_array($navbar->fresh()->items) ? array_values($navbar->fresh()->items) : NavbarConfig::defaultItems();

        if ($key === 'topbarLayout') {
            $isTopEnabled = TopbarController::isTopbarEnabled();
            $responseData = [
                'layout' => $templateId,
                'enabled' => $isTopEnabled,
                'visible' => $isTopEnabled,
                'status' => $isTopEnabled ? 'visible' : 'hidden',
            ];
        } elseif ($key === 'footerLayout') {
            $footer = \App\Models\FooterConfig::getSingleton();
            $footItems = $footer->getItemsList();
            $responseData = [
                'layout'     => $templateId,
                'enabled'    => true,
                'visible'    => true,
                'status'     => 'visible',
                'items'      => $footItems,
                'links'      => $footItems,
                'grid'       => is_array($footer->grid) ? $footer->grid : \App\Models\FooterConfig::defaultGrid(),
                'cta'        => is_array($footer->cta) ? $footer->cta : \App\Models\FooterConfig::defaultCta(),
                'customCode' => is_array($footer->customCode) ? $footer->customCode : \App\Models\FooterConfig::defaultCustomCode(),
            ];
        } else {
            $responseData = [
                'layout' => $templateId,
                'behavior' => $activeBehavior,
                'enabled' => $isEnabled,
                'visible' => $isEnabled,
                'status' => $isEnabled ? 'visible' : 'hidden',
            ];
        }

        if ($key === 'navbarLayout') {
            $responseData['items'] = $navItems;
        }

        return response()->json([
            'success' => true,
            'message' => "{$moduleName} layout updated to {$templateId}",
            'data' => $responseData,
        ], 200);
    }

    // ──────────────────────────────────────────────────────────────────────
    // POST /api/admin/layouts/save — Auth Required (Batch or Single Save)
    // ──────────────────────────────────────────────────────────────────────
    public function save(Request $request): JsonResponse
    {
        $payload = $request->all();

        // Single layout payload e.g. { "layout": "layout5", "behavior": "scroll" }
        if ($request->has('layout') || ($request->has('key') && $request->has('templateId'))) {
            $key = $request->input('key', 'navbarLayout');

            return $this->update($request, $key);
        }

        $items = $request->input('layouts', $payload);

        if (! is_array($items) || empty($items)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided.',
            ], 422);
        }

        // Handle navbarLayout passed as an object { layout: "layout5", behavior: "scroll", items: [...] }
        if (isset($items['navbarLayout']) && is_array($items['navbarLayout'])) {
            $navData = $items['navbarLayout'];
            $items['navbarLayout'] = $navData['layout'] ?? ($navData['templateId'] ?? 'layout1');
            if (isset($navData['behavior'])) {
                $items['behavior'] = $navData['behavior'];
            }
            if (isset($navData['enabled'])) {
                NavbarConfig::getSingleton()->setNavbarEnabled((bool) $navData['enabled']);
            } elseif (isset($navData['visible'])) {
                NavbarConfig::getSingleton()->setNavbarEnabled((bool) $navData['visible']);
            }
            if (isset($navData['items']) && is_array($navData['items'])) {
                $processed = NavbarConfig::processItems($navData['items']);
                NavbarConfig::getSingleton()->update(['items' => $processed]);
            } elseif (isset($navData['menu']) && is_array($navData['menu'])) {
                $processed = NavbarConfig::processItems($navData['menu']);
                NavbarConfig::getSingleton()->update(['items' => $processed]);
            }
        }

        // Handle footerLayout passed as an object { layout: "layout2", items: [...] }
        if (isset($items['footerLayout']) && is_array($items['footerLayout'])) {
            $footData = $items['footerLayout'];
            $items['footerLayout'] = $footData['layout'] ?? ($footData['templateId'] ?? 'layout1');
            $footer = \App\Models\FooterConfig::getSingleton();
            if (isset($footData['items']) && is_array($footData['items'])) {
                $footer->items = $footData['items'];
                $footer->links = $footData['items'];
                $footer->save();
            } elseif (isset($footData['links']) && is_array($footData['links'])) {
                $footer->items = $footData['links'];
                $footer->links = $footData['links'];
                $footer->save();
            }
        }

        if ($request->has('items') && is_array($request->input('items'))) {
            $processed = NavbarConfig::processItems($request->input('items'));
            NavbarConfig::getSingleton()->update(['items' => $processed]);
        } elseif ($request->has('menu') && is_array($request->input('menu'))) {
            $processed = NavbarConfig::processItems($request->input('menu'));
            NavbarConfig::getSingleton()->update(['items' => $processed]);
        }

        if (isset($items['behavior'])) {
            $val = $items['behavior'];
            if (in_array($val, NavbarConfig::$allowedBehaviors)) {
                NavbarConfig::getSingleton()->setBehaviorForLayout($val);
            }
            unset($items['behavior']);
        }

        if (isset($items['behaviors']) && is_array($items['behaviors'])) {
            $navbar = NavbarConfig::getSingleton();
            foreach ($items['behaviors'] as $lKey => $bVal) {
                if (in_array($bVal, NavbarConfig::$allowedBehaviors)) {
                    $navbar->setBehaviorForLayout($bVal, $lKey);
                }
            }
            unset($items['behaviors']);
        }

        foreach ($items as $key => $templateId) {
            if (! in_array($key, Layout::validKeys())) {
                return response()->json([
                    'success' => false,
                    'message' => "Invalid layout key '{$key}'.",
                ], 422);
            }

            $allowed = Layout::allowedFor($key);
            if (! in_array($templateId, $allowed)) {
                return response()->json([
                    'success' => false,
                    'message' => "Invalid templateId '{$templateId}' for '{$key}'. Allowed: ".implode(', ', $allowed),
                ], 422);
            }

            Layout::updateOrCreate(
                ['key' => $key],
                ['templateId' => $templateId, 'updatedAt' => now()]
            );
        }

        $saved = Layout::all()->pluck('templateId', 'key')->toArray();
        $navbar = NavbarConfig::getSingleton();
        $currTemplate = $saved['navbarLayout'] ?? 'layout1';
        $activeBehavior = $navbar->getBehaviorForLayout($currTemplate);
        $isEnabled = $navbar->isNavbarEnabled();
        $navItems = is_array($navbar->fresh()->items) ? array_values($navbar->fresh()->items) : NavbarConfig::defaultItems();

        $saved['navbarLayout'] = [
            'layout' => $currTemplate,
            'behavior' => $activeBehavior,
            'enabled' => $isEnabled,
            'visible' => $isEnabled,
            'status' => $isEnabled ? 'visible' : 'hidden',
            'items' => $navItems,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Layout templates saved successfully.',
            'data' => $saved,
        ], 200);
    }
}
