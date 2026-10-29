<?php

namespace App\Http\Controllers;

use App\Models\Layout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LayoutController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────
    // GET /public/layouts & /api/public/layouts — No Auth
    // Returns all active layouts in one object.
    // ──────────────────────────────────────────────────────────────────────
    public function index(): JsonResponse
    {
        $layouts = Layout::all()->pluck('templateId', 'key');

        return response()->json([
            'success' => true,
            'data'    => $layouts,
        ], 200);
    }

    // ──────────────────────────────────────────────────────────────────────
    // PUT /admin/layouts/:key & /api/admin/layouts/:key — Auth Required
    // Set active template for one key.
    // ──────────────────────────────────────────────────────────────────────
    public function update(Request $request, string $key): JsonResponse
    {
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

        // 2. templateId must be within that key's allowed range, otherwise 422
        $allowed = Layout::allowedFor($key);
        $request->validate([
            'templateId' => [
                'required',
                'string',
                Rule::in($allowed),
            ],
        ], [
            'templateId.required' => 'The templateId field is required.',
            'templateId.in'       => 'The templateId must be within the allowed range (' . implode(', ', $allowed) . ').',
        ]);

        $templateId = $request->input('templateId');

        // 3. Update or insert the layout key
        Layout::updateOrCreate(
            ['key' => $key],
            [
                'templateId' => $templateId,
                'updatedAt'  => now(),
            ]
        );

        // 4. Message names the module and the new template
        $moduleName = Layout::moduleName($key);

        return response()->json([
            'success' => true,
            'message' => "{$moduleName} layout updated to {$templateId}",
        ], 200);
    }

    // ──────────────────────────────────────────────────────────────────────
    // POST /api/admin/layouts/save — Auth Required (Batch or Single Save)
    // ──────────────────────────────────────────────────────────────────────
    public function save(Request $request): JsonResponse
    {
        $payload = $request->all();

        // Single key/templateId payload
        if ($request->has('key') && $request->has('templateId')) {
            return $this->update($request, $request->input('key'));
        }

        $items = $request->input('layouts', $payload);

        if (! is_array($items) || empty($items)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided.',
            ], 422);
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

        return response()->json([
            'success' => true,
            'message' => 'Layout templates saved successfully.',
            'data'    => Layout::all()->pluck('templateId', 'key'),
        ], 200);
    }
}
