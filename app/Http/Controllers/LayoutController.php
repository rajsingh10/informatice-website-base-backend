<?php

namespace App\Http\Controllers;

use App\Models\Layout;
use App\Models\NavbarConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LayoutController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────
    // GET /public/layouts & /api/public/layouts — No Auth
    // Returns all active layouts in one object with navbarLayout as { layout, behavior }.
    // ──────────────────────────────────────────────────────────────────────
    public function index(): JsonResponse
    {
        $layouts = Layout::all()->pluck('templateId', 'key')->toArray();
        $navbar = NavbarConfig::getSingleton();

        $currentNavbarTemplate = $layouts['navbarLayout'] ?? 'layout1';
        $activeBehavior = $navbar->getBehaviorForLayout($currentNavbarTemplate);

        $layouts['navbarLayout'] = [
            'layout'   => $currentNavbarTemplate,
            'behavior' => $activeBehavior,
        ];

        return response()->json([
            'success' => true,
            'data'    => $layouts,
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
        $active  = [];

        foreach (Layout::$modules as $key => $meta) {
            $current = $records->get($key)?->templateId ?? 'layout1';

            if ($key === 'navbarLayout') {
                $active[$key] = [
                    'layout'   => $current,
                    'behavior' => $navbar->getBehaviorForLayout($current),
                ];
            } else {
                $active[$key] = $current;
            }

            $modules[] = [
                'key'        => $key,
                'name'       => $meta['name'],
                'templateId' => $current,
                'allowed'    => Layout::allowedFor($key),
                'updatedAt'  => $records->get($key)?->updatedAt,
            ];
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'active'  => $active,
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
                    'message' => 'Behavior must be one of: ' . implode(', ', NavbarConfig::$allowedBehaviors),
                    'errors'  => ['behavior' => ['Allowed: fixed, scroll, colored, transparent']],
                ], 422);
            }
            $navbar = NavbarConfig::getSingleton();
            $navbar->setBehaviorForLayout($val, $targetLayout);
            return response()->json([
                'success' => true,
                'message' => "Navbar behavior updated to {$val}",
                'data'    => [
                    'layout'          => $targetLayout ?: ($navbar->getBehaviorForLayout() ?? 'layout1'),
                    'behavior'        => $val,
                    'navbarBehavior'  => $val,
                    'navbarBehaviors' => $navbar->getBehaviorsMap(),
                ],
            ], 200);
        }

        // 1. :key must be in the specification table, otherwise 422
        if (! in_array($key, Layout::validKeys())) {
            return response()->json([
                'success' => false,
                'message' => "Invalid layout key '{$key}'.",
                'errors'  => [
                    'key' => [
                        'The key must be one of: ' . implode(', ', Layout::validKeys()) . '.',
                    ],
                ],
            ], 422);
        }

        // 2. templateId or layout must be within that key's allowed range
        $allowed = Layout::allowedFor($key);
        $templateId = $request->input('layout', $request->input('templateId'));

        if (! $templateId || ! in_array($templateId, $allowed)) {
            return response()->json([
                'success' => false,
                'message' => "The layout/templateId must be within the allowed range (" . implode(', ', $allowed) . ").",
                'errors'  => [
                    'layout' => ['The layout/templateId field must be one of: ' . implode(', ', $allowed) . '.'],
                ],
            ], 422);
        }

        // 3. Update or insert the layout key
        Layout::updateOrCreate(
            ['key' => $key],
            [
                'templateId' => $templateId,
                'updatedAt'  => now(),
            ]
        );

        // 4. If navbarLayout was updated, sync active behavior
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
        }

        // 5. Message names the module and the new template
        $moduleName = Layout::moduleName($key);
        $activeBehavior = $navbar->getBehaviorForLayout($templateId);

        $responseData = [
            'layout'   => $templateId,
            'behavior' => $activeBehavior,
        ];

        return response()->json([
            'success' => true,
            'message' => "{$moduleName} layout updated to {$templateId}",
            'data'    => $responseData,
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

        // Handle navbarLayout passed as an object { layout: "layout5", behavior: "scroll" }
        if (isset($items['navbarLayout']) && is_array($items['navbarLayout'])) {
            $navData = $items['navbarLayout'];
            $items['navbarLayout'] = $navData['layout'] ?? ($navData['templateId'] ?? 'layout1');
            if (isset($navData['behavior'])) {
                $items['behavior'] = $navData['behavior'];
            }
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
                    'message' => "Invalid templateId '{$templateId}' for '{$key}'. Allowed: " . implode(', ', $allowed),
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

        $saved['navbarLayout'] = [
            'layout'   => $currTemplate,
            'behavior' => $activeBehavior,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Layout templates saved successfully.',
            'data'    => $saved,
        ], 200);
    }
}
