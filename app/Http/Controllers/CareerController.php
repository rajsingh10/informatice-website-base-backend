<?php

namespace App\Http\Controllers;

use App\Models\Career;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    /**
     * GET /api/public/careers & /public/careers
     * Public feed of open job positions.
     */
    public function indexPublic(Request $request): JsonResponse
    {
        $query = Career::open()
            ->department($request->input('department'))
            ->type($request->input('type'))
            ->location($request->input('location'))
            ->search($request->input('q') ?? $request->input('search'))
            ->orderBy('order', 'asc')
            ->orderBy('postedAt', 'desc')
            ->orderBy('id', 'desc');

        $limit = max(1, min(100, (int) $request->input('limit', 20)));
        $items = $query->limit($limit)->get()->map(fn ($c) => $c->toPublicPayload());

        return response()->json([
            'success' => true,
            'total' => $items->count(),
            'data' => $items,
        ], 200);
    }

    /**
     * GET /api/public/careers/{slug} & /public/careers/{slug}
     * Single public job position detail by slug.
     */
    public function showPublic(string $slug): JsonResponse
    {
        $career = Career::open()->where('slug', $slug)->first();

        if (! $career) {
            return response()->json([
                'success' => false,
                'message' => 'Job opening not found or currently closed.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $career->toPublicPayload(),
        ], 200);
    }

    /**
     * GET /api/admin/careers & /admin/careers
     * Admin list of all positions (open and closed).
     */
    public function indexAdmin(Request $request): JsonResponse
    {
        $query = Career::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->department($request->input('department'))
            ->type($request->input('type'))
            ->search($request->input('q') ?? $request->input('search'))
            ->orderBy('order', 'asc')
            ->orderBy('id', 'desc');

        $items = $query->get()->map(fn ($c) => $c->toAdminPayload());

        return response()->json([
            'success' => true,
            'total' => $items->count(),
            'data' => $items,
        ], 200);
    }

    /**
     * GET /api/admin/careers/{id} & /admin/careers/{id}
     * Single position detail for admin.
     */
    public function showAdmin(int $id): JsonResponse
    {
        $career = Career::find($id);

        if (! $career) {
            return response()->json([
                'success' => false,
                'message' => 'Job opening not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $career->toAdminPayload(),
        ], 200);
    }

    /**
     * POST /api/admin/careers & /admin/careers
     * Create a new job opening.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate(Career::validationRules(false));

        if (empty($validated['slug'])) {
            $validated['slug'] = Career::generateUniqueSlug($validated['title']);
        }

        if (empty($validated['postedAt'])) {
            $validated['postedAt'] = now();
        }

        $career = Career::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Job opening created successfully.',
            'data' => $career->toAdminPayload(),
        ], 201);
    }

    /**
     * PUT /api/admin/careers/{id} & /admin/careers/{id}
     * Update an existing job opening.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $career = Career::find($id);

        if (! $career) {
            return response()->json([
                'success' => false,
                'message' => 'Job opening not found.',
            ], 404);
        }

        $validated = $request->validate(Career::validationRules(true, $id));

        if (isset($validated['slug']) && blank($validated['slug'])) {
            unset($validated['slug']);
        }

        $career->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Job opening updated successfully.',
            'data' => $career->toAdminPayload(),
        ], 200);
    }

    /**
     * DELETE /api/admin/careers/{id} & /admin/careers/{id}
     * Delete a job opening.
     */
    public function destroy(int $id): JsonResponse
    {
        $career = Career::find($id);

        if (! $career) {
            return response()->json([
                'success' => false,
                'message' => 'Job opening not found.',
            ], 404);
        }

        $career->delete();

        return response()->json([
            'success' => true,
            'message' => "Job opening '{$career->title}' deleted successfully.",
        ], 200);
    }
}
