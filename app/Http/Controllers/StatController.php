<?php

namespace App\Http\Controllers;

use App\Models\Stat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatController extends Controller
{
    /**
     * GET /api/admin/stats or GET /api/stats
     * Fetch all metrics/counters list.
     */
    public function indexAdmin(): JsonResponse
    {
        Stat::seedDefaultsIfEmpty();

        $stats = Stat::orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->map(fn (Stat $s) => $s->toPayload());

        return response()->json([
            'success' => true,
            'message' => 'Stats fetched successfully',
            'data'    => $stats,
        ], 200);
    }

    /**
     * GET /api/public/stats
     * Fetch visible public metrics/counters for the website.
     */
    public function indexPublic(): JsonResponse
    {
        Stat::seedDefaultsIfEmpty();

        $stats = Stat::where('isFeatured', true)
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->map(fn (Stat $s) => $s->toPayload());

        return response()->json([
            'success' => true,
            'message' => 'Stats fetched successfully',
            'data'    => $stats,
        ], 200);
    }

    /**
     * GET /api/admin/stats/:id
     * Fetch a single stat counter by ID.
     */
    public function show(int $id): JsonResponse
    {
        $stat = Stat::find($id);

        if (! $stat) {
            return response()->json([
                'success' => false,
                'message' => "Stat counter with ID {$id} not found",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $stat->toPayload(),
        ], 200);
    }

    /**
     * POST /api/admin/stats
     * Add a new stat counter.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'number'      => 'required|numeric',
            'suffix'      => 'nullable|string|max:20',
            'label'       => 'required|string|max:150',
            'icon'        => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'isFeatured'  => 'nullable|boolean',
            'visible'     => 'nullable|boolean',
            'order'       => 'nullable|integer',
        ]);

        $isFeatured = true;
        if ($request->has('isFeatured')) {
            $isFeatured = filter_var($request->input('isFeatured'), FILTER_VALIDATE_BOOLEAN);
        } elseif ($request->has('visible')) {
            $isFeatured = filter_var($request->input('visible'), FILTER_VALIDATE_BOOLEAN);
        }

        $order = $request->input('order');
        if ($order === null || $order === '') {
            $order = ((int) Stat::max('order')) + 1;
        }

        $stat = Stat::create([
            'number'      => (float) $validated['number'],
            'suffix'      => (string) ($validated['suffix'] ?? '+'),
            'label'       => trim($validated['label']),
            'icon'        => trim($validated['icon'] ?? 'Briefcase'),
            'description' => $validated['description'] ?? null,
            'isFeatured'  => $isFeatured,
            'order'       => (int) $order,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stat counter added successfully',
            'data'    => $stat->toPayload(),
        ], 201);
    }

    /**
     * PUT / PATCH /api/admin/stats/:id
     * Update an existing stat counter.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $stat = Stat::find($id);

        if (! $stat) {
            return response()->json([
                'success' => false,
                'message' => "Stat counter with ID {$id} not found",
            ], 404);
        }

        $validated = $request->validate([
            'number'      => 'nullable|numeric',
            'suffix'      => 'nullable|string|max:20',
            'label'       => 'nullable|string|max:150',
            'icon'        => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'isFeatured'  => 'nullable|boolean',
            'visible'     => 'nullable|boolean',
            'order'       => 'nullable|integer',
        ]);

        if (array_key_exists('number', $validated)) {
            $stat->number = (float) $validated['number'];
        }

        if (array_key_exists('suffix', $validated)) {
            $stat->suffix = (string) $validated['suffix'];
        }

        if (isset($validated['label'])) {
            $stat->label = trim($validated['label']);
        }

        if (array_key_exists('icon', $validated)) {
            $stat->icon = trim((string) $validated['icon']);
        }

        if (array_key_exists('description', $validated)) {
            $stat->description = $validated['description'];
        }

        if ($request->has('isFeatured')) {
            $stat->isFeatured = filter_var($request->input('isFeatured'), FILTER_VALIDATE_BOOLEAN);
        } elseif ($request->has('visible')) {
            $stat->isFeatured = filter_var($request->input('visible'), FILTER_VALIDATE_BOOLEAN);
        }

        if ($request->has('order') && $request->input('order') !== null) {
            $stat->order = (int) $request->input('order');
        }

        $stat->save();

        return response()->json([
            'success' => true,
            'message' => 'Stat counter updated successfully',
            'data'    => $stat->toPayload(),
        ], 200);
    }

    /**
     * DELETE /api/admin/stats/:id
     * Delete a stat counter.
     */
    public function destroy(int $id): JsonResponse
    {
        $stat = Stat::find($id);

        if (! $stat) {
            return response()->json([
                'success' => false,
                'message' => "Stat counter with ID {$id} not found",
            ], 404);
        }

        $label = $stat->label;
        $stat->delete();

        return response()->json([
            'success' => true,
            'message' => "Stat counter '{$label}' deleted successfully",
        ], 200);
    }

    /**
     * PUT / PATCH /api/admin/stats/reorder
     * Swap / Reorder sequence for display.
     */
    public function reorder(Request $request): JsonResponse
    {
        $items = $request->input('items') 
            ?? $request->input('orders') 
            ?? (is_array($request->all()) && ! isset($request->all()['items']) && isset($request->all()[0]) ? $request->all() : []);

        if (is_array($items)) {
            foreach ($items as $index => $item) {
                if (is_array($item) && isset($item['id'])) {
                    $order = isset($item['order']) ? (int) $item['order'] : ($index + 1);
                    Stat::where('id', $item['id'])->update(['order' => $order]);
                } elseif (is_numeric($item)) {
                    // Passed an array of IDs: [1, 3, 2, 4]
                    Stat::where('id', (int) $item)->update(['order' => $index + 1]);
                }
            }
        }

        $updatedList = Stat::orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->map(fn (Stat $s) => $s->toPayload());

        return response()->json([
            'success' => true,
            'message' => 'Stats reordered successfully',
            'data'    => $updatedList,
        ], 200);
    }

    /**
     * PATCH /api/admin/stats/:id/visibility
     * Toggle Active / Visible (isFeatured) switch.
     */
    public function toggleVisibility(Request $request, int $id): JsonResponse
    {
        $stat = Stat::find($id);

        if (! $stat) {
            return response()->json([
                'success' => false,
                'message' => "Stat counter with ID {$id} not found",
            ], 404);
        }

        if ($request->has('isFeatured')) {
            $stat->isFeatured = filter_var($request->input('isFeatured'), FILTER_VALIDATE_BOOLEAN);
        } elseif ($request->has('visible')) {
            $stat->isFeatured = filter_var($request->input('visible'), FILTER_VALIDATE_BOOLEAN);
        } else {
            // Toggle current state if no boolean provided
            $stat->isFeatured = ! $stat->isFeatured;
        }

        $stat->save();

        return response()->json([
            'success' => true,
            'message' => 'Stat visibility updated successfully',
            'data'    => $stat->toPayload(),
        ], 200);
    }
}

