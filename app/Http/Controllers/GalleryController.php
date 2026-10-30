<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GalleryController extends Controller
{
    /**
     * Public list of gallery albums with photo items.
     * Supports ?category=, ?limit=, ?search=.
     * Only returns published albums sorted by order asc, id asc.
     */
    public function indexPublic(Request $request): JsonResponse
    {
        $query = Gallery::published();

        if ($request->filled('category')) {
            $query->category($request->query('category'));
        }

        if ($request->has('featured') || $request->has('isFeatured')) {
            $query->where('isFeatured', filter_var($request->query('featured', $request->query('isFeatured')), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $search = '%'.$request->query('search').'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('category', 'like', $search)
                    ->orWhere('description', 'like', $search);
            });
        }

        $total = $query->count();
        $query->orderBy('order', 'asc')->orderBy('id', 'asc');

        if ($request->filled('limit')) {
            $limit = max(1, (int) $request->query('limit', 20));
            $page = max(1, (int) $request->query('page', 1));
            $albums = $query->forPage($page, $limit)->get();
        } else {
            $albums = $query->get();
        }

        return response()->json([
            'success' => true,
            'total' => $total,
            'data' => $albums->map(fn ($album) => $album->toPublicPayload()),
        ], 200);
    }

    /**
     * Public single gallery album by id.
     */
    public function showPublic(int $id): JsonResponse
    {
        $album = Gallery::published()->find($id);

        if (! $album) {
            return response()->json([
                'success' => false,
                'message' => 'Gallery album not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $album->toPublicPayload(),
        ], 200);
    }

    /**
     * Admin list of all gallery albums.
     * Returns both published and unpublished albums.
     */
    public function indexAdmin(Request $request): JsonResponse
    {
        $query = Gallery::query();

        if ($request->filled('category')) {
            $query->category($request->query('category'));
        }

        if ($request->has('featured') || $request->has('isFeatured')) {
            $query->where('isFeatured', filter_var($request->query('featured', $request->query('isFeatured')), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->has('isPublished')) {
            $isPublished = filter_var($request->query('isPublished'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($isPublished !== null) {
                $query->where('isPublished', $isPublished);
            }
        }

        if ($request->filled('search')) {
            $search = '%'.$request->query('search').'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('category', 'like', $search)
                    ->orWhere('description', 'like', $search);
            });
        }

        $total = $query->count();
        $query->orderBy('order', 'asc')->orderBy('id', 'asc');

        if ($request->filled('limit')) {
            $limit = max(1, (int) $request->query('limit', 20));
            $page = max(1, (int) $request->query('page', 1));
            $albums = $query->forPage($page, $limit)->get();
        } else {
            $albums = $query->get();
        }

        return response()->json([
            'success' => true,
            'total' => $total,
            'data' => $albums->map(fn ($album) => $album->toAdminPayload()),
        ], 200);
    }

    /**
     * Admin single gallery album by id.
     */
    public function showAdmin(int $id): JsonResponse
    {
        $album = Gallery::find($id);

        if (! $album) {
            return response()->json([
                'success' => false,
                'message' => 'Gallery album not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $album->toAdminPayload(),
        ], 200);
    }

    /**
     * Admin create a new gallery album (can include images).
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), Gallery::validationRules(false));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if (! isset($data['order']) || $data['order'] === null) {
            $maxOrder = Gallery::max('order');
            $data['order'] = $maxOrder !== null ? $maxOrder + 1 : 1;
        }

        if (! isset($data['isPublished'])) {
            $data['isPublished'] = true;
        }

        if (isset($data['images']) && is_array($data['images'])) {
            $data['images'] = Gallery::normalizeImages($data['images']);
        } else {
            $data['images'] = [];
        }

        $album = Gallery::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Gallery album created successfully.',
            'data' => $album->toAdminPayload(),
        ], 201);
    }

    /**
     * Admin update gallery album and its images.
     * PUT replaces the images array (same pattern as navbar items).
     * Images sent without id are created, missing ones are removed.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $album = Gallery::find($id);

        if (! $album) {
            return response()->json([
                'success' => false,
                'message' => 'Gallery album not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), Gallery::validationRules(true));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // If images array is explicitly provided, normalize and replace
        if (array_key_exists('images', $data) && is_array($data['images'])) {
            $data['images'] = Gallery::normalizeImages($data['images']);
        }

        $album->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Gallery album updated successfully.',
            'data' => $album->fresh()->toAdminPayload(),
        ], 200);
    }

    /**
     * Admin delete gallery album.
     */
    public function destroy(int $id): JsonResponse
    {
        $album = Gallery::find($id);

        if (! $album) {
            return response()->json([
                'success' => false,
                'message' => 'Gallery album not found.',
            ], 404);
        }

        $album->delete();

        return response()->json([
            'success' => true,
            'message' => 'Gallery album deleted successfully.',
        ], 200);
    }
}
