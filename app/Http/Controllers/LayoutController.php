<?php

namespace App\Http\Controllers;

use App\Models\Layout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LayoutController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────
    // GET /api/public/layouts   — No Auth
    // Returns all active layouts as a flat key → templateId object.
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
    // POST /api/admin/layouts   — Auth Required
    // Create a new layout key with its templateId.
    // ──────────────────────────────────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key'        => 'required|string|max:100|unique:layouts,key',
            'templateId' => 'required|string|max:100',
        ], [
            'key.unique' => 'This layout key already exists. Use PUT /api/admin/layouts/' . $request->input('key') . ' to update it.',
        ]);

        $key = $validated['key'];
        $templateId = $validated['templateId'];

        // If the key is one of the predefined ones, validate allowed templates
        $allowed = Layout::allowedFor($key);
        if (! empty($allowed) && ! in_array($templateId, $allowed)) {
            return response()->json([
                'success' => false,
                'message' => 'The templateId must be one of: ' . implode(', ', $allowed) . '.',
                'errors'  => [
                    'templateId' => ['The templateId must be one of: ' . implode(', ', $allowed) . '.'],
                ],
            ], 422);
        }

        $layout = Layout::create([
            'key'        => $key,
            'templateId' => $templateId,
            'updatedAt'  => now(),
        ]);

        $moduleName = $this->humanize($key);

        return response()->json([
            'success' => true,
            'message' => "{$moduleName} layout created with {$templateId}",
            'data'    => [
                'key'        => $layout->key,
                'templateId' => $layout->templateId,
                'updatedAt'  => $layout->updatedAt,
            ],
        ], 201);
    }

    // ──────────────────────────────────────────────────────────────────────
    // POST /api/admin/layouts/save   — Auth Required
    // Save / batch update one or multiple layout templates at once.
    // Supports:
    // 1) Single key-value: { "key": "topbarLayout", "templateId": "layout2" }
    // 2) Batch object:     { "topbarLayout": "layout2", "navbarLayout": "layout1" }
    // 3) Wrapped batch:    { "layouts": { "topbarLayout": "layout2", ... } }
    // ──────────────────────────────────────────────────────────────────────
    public function save(Request $request): JsonResponse
    {
        $payload = $request->all();

        // Case 1: single item with { "key": "...", "templateId": "..." }
        if ($request->has('key') && $request->has('templateId')) {
            return $this->update($request, $request->input('key'));
        }

        // Case 2: wrapped in { "layouts": { ... } }
        $items = $request->input('layouts', $payload);

        if (! is_array($items) || empty($items)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided. Send a key-value map of layouts or { key, templateId }.',
            ], 422);
        }

        $updated = [];
        foreach ($items as $key => $templateId) {
            if (! is_string($key) || ! is_string($templateId)) {
                continue;
            }

            // If allowed range exists, check it
            $allowed = Layout::allowedFor($key);
            if (! empty($allowed) && ! in_array($templateId, $allowed)) {
                return response()->json([
                    'success' => false,
                    'message' => "Invalid templateId '{$templateId}' for '{$key}'. Allowed: " . implode(', ', $allowed),
                ], 422);
            }

            Layout::updateOrCreate(
                ['key' => $key],
                ['templateId' => $templateId, 'updatedAt' => now()]
            );

            $updated[$key] = $templateId;
        }

        return response()->json([
            'success' => true,
            'message' => 'Layout templates saved successfully.',
            'data'    => Layout::all()->pluck('templateId', 'key'),
        ], 200);
    }

    // ──────────────────────────────────────────────────────────────────────
    // PUT /api/admin/layouts/:key   — Auth Required
    // Set the active template for one layout key.
    // ──────────────────────────────────────────────────────────────────────
    public function update(Request $request, string $key): JsonResponse
    {
        // 1. Validate the :key param against valid keys
        if (! in_array($key, Layout::validKeys())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid layout key.',
                'errors'  => [
                    'key' => [
                        'The key must be one of: ' . implode(', ', Layout::validKeys()) . '.',
                    ],
                ],
            ], 422);
        }

        // 2. Validate templateId (if key has predefined range, validate it; otherwise max:100)
        $allowed = Layout::allowedFor($key);
        if (! empty($allowed)) {
            $request->validate([
                'templateId' => [
                    'required',
                    'string',
                    Rule::in($allowed),
                ],
            ], [
                'templateId.in' => 'The templateId must be one of: '
                    . implode(', ', $allowed) . '.',
            ]);
        } else {
            $request->validate([
                'templateId' => ['required', 'string', 'max:100'],
            ]);
        }

        $templateId = $request->input('templateId');

        // 3. Upsert — create or update the row
        Layout::updateOrCreate(
            ['key' => $key],
            ['templateId' => $templateId, 'updatedAt' => now()]
        );

        // 4. Build a human-readable module name (topbarLayout → Topbar)
        $moduleName = $this->humanize($key);

        return response()->json([
            'success' => true,
            'message' => "{$moduleName} layout updated to {$templateId}",
        ], 200);
    }

    // ──────────────────────────────────────────────────────────────────────
    // Helper: "topbarLayout" → "Topbar"
    // ──────────────────────────────────────────────────────────────────────
    private function humanize(string $key): string
    {
        // Remove trailing "Layout" and capitalise
        $name = preg_replace('/Layout$/i', '', $key);
        return ucfirst($name ?: $key);
    }
}
