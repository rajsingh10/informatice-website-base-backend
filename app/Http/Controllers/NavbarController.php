<?php

namespace App\Http\Controllers;

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
            'data'    => $config->toPublicPayload(),
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
            'data'    => $config->toAdminPayload(),
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
                'behavior.in' => 'Behavior must be one of: ' . implode(', ', NavbarConfig::$allowedBehaviors),
                'headerBehavior.in' => 'headerBehavior must be one of: ' . implode(', ', NavbarConfig::$allowedBehaviors),
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
                    'label'   => (string) ($ctaInput['label'] ?? 'Get a Quote'),
                    'url'     => (string) ($ctaInput['url'] ?? '/contact'),
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
            $allowed = \App\Models\Layout::allowedFor('navbarLayout');
            if (in_array($layoutId, $allowed)) {
                \App\Models\Layout::updateOrCreate(
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
            'data'    => $config->fresh()->toPublicPayload(),
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
                Rule::in(\App\Models\Layout::allowedFor('navbarLayout')),
            ],
        ], [
            'behavior.in' => 'Behavior must be one of: ' . implode(', ', NavbarConfig::$allowedBehaviors),
        ]);

        $behavior = $request->input('behavior');
        $targetLayout = $request->input('layout');

        $config = NavbarConfig::getSingleton();
        $config->setBehaviorForLayout($behavior, $targetLayout);

        return response()->json([
            'success' => true,
            'message' => "Navbar behavior updated to {$behavior}.",
            'data'    => [
                'behavior'        => $behavior,
                'headerBehavior'  => $behavior,
                'behaviors'       => $config->getBehaviorsMap(),
                'navbarBehaviors' => $config->getBehaviorsMap(),
            ],
        ], 200);
    }

    /**
     * PUT /admin/navigation/navbar/items — Auth Required
     * Replace the full menu array. Server preserves array position order,
     * assigns ID to new items, deletes missing items.
     */
    public function updateItems(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.label' => 'required|string|max:100',
            'items.*.url'   => 'required|string|max:255',
            'items.*.enabled' => 'sometimes|boolean',
            'items.*.id'    => 'sometimes|nullable|string',
        ]);

        $processed = NavbarConfig::processItems($request->input('items', []));

        $config = NavbarConfig::getSingleton();
        $config->update(['items' => $processed]);

        return response()->json([
            'success' => true,
            'message' => 'Navbar menu items updated successfully.',
            'data'    => [
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
            'label'   => 'required|string|max:100',
            'url'     => 'required|string|max:255',
        ]);

        $cta = [
            'enabled' => (bool) $request->input('enabled'),
            'label'   => (string) $request->input('label'),
            'url'     => (string) $request->input('url'),
        ];

        $config = NavbarConfig::getSingleton();
        $config->update(['cta' => $cta]);

        return response()->json([
            'success' => true,
            'message' => 'Navbar CTA button updated successfully.',
            'data'    => [
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
            'data'    => $config->fresh()->toAdminPayload(),
        ], 200);
    }
}

