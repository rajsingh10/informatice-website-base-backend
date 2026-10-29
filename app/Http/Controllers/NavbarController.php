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
        ], [
            'behavior.in' => 'Behavior must be one of: ' . implode(', ', NavbarConfig::$allowedBehaviors),
        ]);

        $behavior = $request->input('behavior');

        $config = NavbarConfig::getSingleton();
        $config->update(['headerBehavior' => $behavior]);

        return response()->json([
            'success' => true,
            'message' => "Navbar behavior updated to {$behavior}.",
            'data'    => [
                'headerBehavior' => $behavior,
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

