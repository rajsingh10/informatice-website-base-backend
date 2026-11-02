<?php

namespace App\Http\Controllers;

use App\Models\HeroSlide;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HeroSlideController extends Controller
{
    /**
     * GET /api/public/hero-slides & /api/public/hero
     * Public list of published hero slides.
     * Query parameters:
     *   - featured: (bool|int) filter only featured slides
     *   - limit: (int) limit number of slides
     */
    public function indexPublic(Request $request): JsonResponse
    {
        $query = HeroSlide::where('isPublished', true);

        if ($request->has('featured') || $request->has('isFeatured')) {
            $isFeatured = filter_var($request->query('isFeatured', $request->query('featured')), FILTER_VALIDATE_BOOLEAN);
            if ($isFeatured) {
                $query->where('isFeatured', true);
            }
        }

        $query->orderBy('order', 'asc')->orderBy('id', 'asc');

        if ($request->filled('limit')) {
            $limit = max(1, min(50, (int) $request->query('limit')));
            $query->limit($limit);
        }

        $slides = $query->get()->map->toPublicPayload()->values();

        return response()->json([
            'success' => true,
            'total'   => count($slides),
            'data'    => $slides,
            'slides'  => $slides,
        ], 200);
    }

    /**
     * GET /api/admin/hero-slides & /api/admin/hero
     * Admin list of all hero slides.
     */
    public function indexAdmin(Request $request): JsonResponse
    {
        $query = HeroSlide::query();

        if ($request->filled('search')) {
            $search = '%' . trim((string) $request->query('search')) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('subtitle', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
        }

        if ($request->has('isFeatured')) {
            $query->where('isFeatured', filter_var($request->query('isFeatured'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->has('isPublished')) {
            $query->where('isPublished', filter_var($request->query('isPublished'), FILTER_VALIDATE_BOOLEAN));
        }

        $slides = $query->orderBy('order', 'asc')->orderBy('id', 'asc')->get()->map->toAdminPayload()->values();

        return response()->json([
            'success' => true,
            'total'   => count($slides),
            'data'    => $slides,
            'slides'  => $slides,
        ], 200);
    }

    /**
     * GET /api/admin/hero-slides/{id}
     * Get single hero slide for editing.
     */
    public function show($id): JsonResponse
    {
        $slide = HeroSlide::find($id);

        if (! $slide) {
            return response()->json([
                'success' => false,
                'message' => "Hero slide #{$id} not found",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $slide->toAdminPayload(),
        ], 200);
    }

    /**
     * POST /api/admin/hero-slides
     * Create a new hero slide.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title'               => 'required|string|max:255',
            'subtitle'            => 'nullable|string',
            'description'         => 'nullable|string',
            'isFeatured'          => 'nullable|boolean',
            'featured'            => 'nullable|boolean',
            'primaryButtonText'   => 'nullable|string|max:100',
            'primaryButtonUrl'    => 'nullable|string|max:255',
            'secondaryButtonText' => 'nullable|string|max:100',
            'secondaryButtonUrl'  => 'nullable|string|max:255',
            'image'               => 'nullable|string',
            'imageAlt'            => 'nullable|string|max:255',
            'order'               => 'nullable|integer',
            'isPublished'         => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $request->only([
            'title',
            'primaryButtonText',
            'primaryButtonUrl',
            'secondaryButtonText',
            'secondaryButtonUrl',
            'image',
            'imageAlt',
        ]);

        $data['subtitle'] = $request->input('subtitle', $request->input('description', ''));
        $data['description'] = $request->input('description', $request->input('subtitle', ''));

        // Handle isFeatured / featured
        if ($request->has('isFeatured')) {
            $data['isFeatured'] = filter_var($request->input('isFeatured'), FILTER_VALIDATE_BOOLEAN);
        } elseif ($request->has('featured')) {
            $data['isFeatured'] = filter_var($request->input('featured'), FILTER_VALIDATE_BOOLEAN);
        } else {
            $data['isFeatured'] = true;
        }

        // Handle isPublished
        if ($request->has('isPublished')) {
            $data['isPublished'] = filter_var($request->input('isPublished'), FILTER_VALIDATE_BOOLEAN);
        } else {
            $data['isPublished'] = true;
        }

        // Handle order
        $data['order'] = (int) $request->input('order', 0);

        $slide = HeroSlide::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Hero slide created successfully',
            'data'    => $slide->toAdminPayload(),
        ], 201);
    }

    /**
     * PUT|PATCH /api/admin/hero-slides/{id}
     * Update an existing hero slide.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $slide = HeroSlide::find($id);

        if (! $slide) {
            return response()->json([
                'success' => false,
                'message' => "Hero slide #{$id} not found",
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title'               => 'sometimes|required|string|max:255',
            'subtitle'            => 'nullable|string',
            'description'         => 'nullable|string',
            'isFeatured'          => 'nullable|boolean',
            'featured'            => 'nullable|boolean',
            'primaryButtonText'   => 'nullable|string|max:100',
            'primaryButtonUrl'    => 'nullable|string|max:255',
            'secondaryButtonText' => 'nullable|string|max:100',
            'secondaryButtonUrl'  => 'nullable|string|max:255',
            'image'               => 'nullable|string',
            'imageAlt'            => 'nullable|string|max:255',
            'order'               => 'nullable|integer',
            'isPublished'         => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $payload = $request->all();

        if ($request->has('title')) {
            $slide->title = (string) $request->input('title');
        }

        if ($request->has('subtitle') || $request->has('description')) {
            $sub = $request->input('subtitle', $request->input('description'));
            $slide->subtitle = $sub;
            $slide->description = $sub;
        }

        if ($request->has('isFeatured')) {
            $slide->isFeatured = filter_var($request->input('isFeatured'), FILTER_VALIDATE_BOOLEAN);
        } elseif ($request->has('featured')) {
            $slide->isFeatured = filter_var($request->input('featured'), FILTER_VALIDATE_BOOLEAN);
        }

        if ($request->has('primaryButtonText')) {
            $slide->primaryButtonText = $request->input('primaryButtonText');
        }
        if ($request->has('primaryButtonUrl')) {
            $slide->primaryButtonUrl = $request->input('primaryButtonUrl');
        }
        if ($request->has('secondaryButtonText')) {
            $slide->secondaryButtonText = $request->input('secondaryButtonText');
        }
        if ($request->has('secondaryButtonUrl')) {
            $slide->secondaryButtonUrl = $request->input('secondaryButtonUrl');
        }
        if ($request->has('image')) {
            $slide->image = $request->input('image');
        }
        if ($request->has('imageAlt')) {
            $slide->imageAlt = $request->input('imageAlt');
        }
        if ($request->has('order')) {
            $slide->order = (int) $request->input('order');
        }
        if ($request->has('isPublished')) {
            $slide->isPublished = filter_var($request->input('isPublished'), FILTER_VALIDATE_BOOLEAN);
        }

        $slide->save();

        return response()->json([
            'success' => true,
            'message' => 'Hero slide updated successfully',
            'data'    => $slide->toAdminPayload(),
        ], 200);
    }

    /**
     * DELETE /api/admin/hero-slides/{id}
     * Delete a hero slide.
     */
    public function destroy($id): JsonResponse
    {
        $slide = HeroSlide::find($id);

        if (! $slide) {
            return response()->json([
                'success' => false,
                'message' => "Hero slide #{$id} not found",
            ], 404);
        }

        $slide->delete();

        return response()->json([
            'success' => true,
            'message' => "Hero slide #{$id} deleted successfully",
        ], 200);
    }
}

