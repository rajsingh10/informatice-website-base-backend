<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * GET /public/services — No Auth
     * Catalog list with optional category, limit, page filtering.
     * Returns: id, title, slug, category, shortDescription, icon, image, features plus total.
     */
    public function indexPublic(Request $request): JsonResponse
    {
        $query = Service::where('isPublished', true);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        // Search in title or description
        if ($request->filled('search')) {
            $term = '%' . $request->query('search') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('shortDescription', 'like', $term);
            });
        }

        $total = $query->count();

        // Ordering & Pagination
        $limit = max(1, min(100, (int) $request->query('limit', 20)));
        $page = max(1, (int) $request->query('page', 1));
        $offset = ($page - 1) * $limit;

        $services = $query->orderBy('order', 'asc')
                          ->orderBy('id', 'asc')
                          ->skip($offset)
                          ->take($limit)
                          ->get();

        return response()->json([
            'success' => true,
            'total'   => $total,
            'page'    => $page,
            'limit'   => $limit,
            'data'    => $services->map->toCatalogPayload(),
        ], 200);
    }

    /**
     * GET /public/services/:slug — No Auth
     * Returns the full model for a single published service.
     */
    public function showPublic(string $slug): JsonResponse
    {
        $service = Service::where('slug', $slug)
                          ->where('isPublished', true)
                          ->first();

        if (! $service) {
            return response()->json([
                'success' => false,
                'message' => "Service '{$slug}' not found.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $service->toDetailPayload(),
        ], 200);
    }

    /**
     * GET /admin/services — Auth Required
     * Admin list including unpublished services.
     */
    public function indexAdmin(Request $request): JsonResponse
    {
        $query = Service::query();

        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        if ($request->has('isPublished')) {
            $query->where('isPublished', filter_var($request->query('isPublished'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $term = '%' . $request->query('search') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('shortDescription', 'like', $term);
            });
        }

        $total = $query->count();
        $services = $query->orderBy('order', 'asc')
                          ->orderBy('id', 'asc')
                          ->get();

        return response()->json([
            'success' => true,
            'total'   => $total,
            'data'    => $services->map->toAdminPayload(),
        ], 200);
    }

    /**
     * GET /admin/services/:id — Auth Required
     * Admin single record for edit form.
     */
    public function showAdmin(int|string $id): JsonResponse
    {
        $service = Service::find($id);

        if (! $service) {
            return response()->json([
                'success' => false,
                'message' => "Service with ID {$id} not found.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $service->toAdminPayload(),
        ], 200);
    }

    /**
     * POST /admin/services — Auth Required
     * Create a service. Required on create: title.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'sometimes|nullable|string|max:255|unique:services,slug',
            'category'         => 'sometimes|nullable|string|max:100',
            'shortDescription' => 'sometimes|nullable|string',
            'fullDescription'  => 'sometimes|nullable|string',
            'icon'             => 'sometimes|nullable|string|max:255',
            'image'            => 'sometimes|nullable|string|max:1024',
            'features'         => 'sometimes|nullable|array',
            'features.*'       => 'string',
            'deliverables'     => 'sometimes|nullable|array',
            'deliverables.*'   => 'string',
            'order'            => 'sometimes|integer',
            'isPublished'      => 'sometimes|boolean',
            'seo'              => 'sometimes|nullable|array',
        ], [
            'title.required' => 'The title field is required.',
            'slug.unique'    => 'This slug is already taken. Please provide a unique slug or leave it blank.',
        ]);

        $slug = $request->filled('slug')
            ? Service::generateSlug($request->input('slug'))
            : Service::generateSlug($request->input('title'));

        $service = Service::create([
            'title'            => $request->input('title'),
            'slug'             => $slug,
            'category'         => $request->input('category', 'General'),
            'shortDescription' => $request->input('shortDescription', ''),
            'fullDescription'  => $request->input('fullDescription', ''),
            'icon'             => $request->input('icon', 'Globe'),
            'image'            => $request->input('image', ''),
            'features'         => $request->input('features', []),
            'deliverables'     => $request->input('deliverables', []),
            'order'            => (int) $request->input('order', 0),
            'isPublished'      => $request->has('isPublished') ? (bool) $request->input('isPublished') : true,
            'seo'              => $request->input('seo', []),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully.',
            'data'    => $service->toAdminPayload(),
        ], 201);
    }

    /**
     * PUT /admin/services/:id — Auth Required
     * Update an existing service.
     */
    public function update(Request $request, int|string $id): JsonResponse
    {
        $service = Service::find($id);

        if (! $service) {
            return response()->json([
                'success' => false,
                'message' => "Service with ID {$id} not found.",
            ], 404);
        }

        $request->validate([
            'title'            => 'sometimes|required|string|max:255',
            'slug'             => 'sometimes|required|string|max:255|unique:services,slug,' . $service->id,
            'category'         => 'sometimes|nullable|string|max:100',
            'shortDescription' => 'sometimes|nullable|string',
            'fullDescription'  => 'sometimes|nullable|string',
            'icon'             => 'sometimes|nullable|string|max:255',
            'image'            => 'sometimes|nullable|string|max:1024',
            'features'         => 'sometimes|nullable|array',
            'features.*'       => 'string',
            'deliverables'     => 'sometimes|nullable|array',
            'deliverables.*'   => 'string',
            'order'            => 'sometimes|integer',
            'isPublished'      => 'sometimes|boolean',
            'seo'              => 'sometimes|nullable|array',
        ]);

        $data = $request->only([
            'title',
            'category',
            'shortDescription',
            'fullDescription',
            'icon',
            'image',
            'features',
            'deliverables',
            'order',
            'isPublished',
            'seo',
        ]);

        if ($request->filled('slug')) {
            $data['slug'] = Service::generateSlug($request->input('slug'), $service->id);
        } elseif ($request->filled('title') && ! $service->slug) {
            $data['slug'] = Service::generateSlug($request->input('title'), $service->id);
        }

        $service->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully.',
            'data'    => $service->fresh()->toAdminPayload(),
        ], 200);
    }

    /**
     * DELETE /admin/services/:id — Auth Required
     * Delete a service.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $service = Service::find($id);

        if (! $service) {
            return response()->json([
                'success' => false,
                'message' => "Service with ID {$id} not found.",
            ], 404);
        }

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => "Service '{$service->title}' deleted successfully.",
        ], 200);
    }
}

