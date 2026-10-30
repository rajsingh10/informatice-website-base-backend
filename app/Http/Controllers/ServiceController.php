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

        if ($request->has('featured') || $request->has('isFeatured')) {
            $query->where('isFeatured', filter_var($request->query('featured', $request->query('isFeatured')), FILTER_VALIDATE_BOOLEAN));
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

        $query->orderBy('order', 'asc')->orderBy('id', 'asc');

        if ($request->filled('limit')) {
            $limit = max(1, min(100, (int) $request->query('limit')));
            $page  = max(1, (int) $request->query('page', 1));
            $services = $query->forPage($page, $limit)->get();
        } else {
            $page  = 1;
            $limit = $total;
            $services = $query->get();
        }

        $catalog = $services->map->toCatalogPayload()->values();

        return response()->json([
            'success'  => true,
            'total'    => $total,
            'page'     => $page,
            'limit'    => $limit,
            'services' => $catalog,
            'data'     => $catalog,
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

        if ($request->has('featured') || $request->has('isFeatured')) {
            $query->where('isFeatured', filter_var($request->query('featured', $request->query('isFeatured')), FILTER_VALIDATE_BOOLEAN));
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

        $adminList = $services->map->toAdminPayload()->values();

        return response()->json([
            'success'  => true,
            'total'    => $total,
            'services' => $adminList,
            'data'     => $adminList,
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
            'imageAlt'         => 'sometimes|nullable|string|max:500',
            'altImage'         => 'sometimes|nullable|string|max:500',
            'features'         => 'sometimes|nullable|array',
            'features.*'       => 'string',
            'deliverables'     => 'sometimes|nullable|array',
            'deliverables.*'   => 'string',
            'order'            => 'sometimes|integer',
            'isFeatured'       => 'sometimes|boolean',
            'isPublished'      => 'sometimes|boolean',
            'seo'              => 'sometimes|nullable|array',
        ], [
            'title.required' => 'The title field is required.',
            'slug.unique'    => 'This slug is already taken. Please provide a unique slug or leave it blank.',
        ]);

        $slug = $request->filled('slug')
            ? Service::generateSlug($request->input('slug'))
            : Service::generateSlug($request->input('title'));

        $imageAlt = (string) $request->input('imageAlt', $request->input('altImage', ''));

        $createData = [
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
            'isFeatured'       => $request->has('isFeatured') ? filter_var($request->input('isFeatured'), FILTER_VALIDATE_BOOLEAN) : false,
            'isPublished'      => $request->has('isPublished') ? filter_var($request->input('isPublished'), FILTER_VALIDATE_BOOLEAN) : true,
            'seo'              => $request->input('seo', []),
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('services', 'imageAlt')) {
            $createData['imageAlt'] = $imageAlt;
        } else {
            $seo = is_array($createData['seo']) ? $createData['seo'] : [];
            $seo['imageAlt'] = $imageAlt;
            $createData['seo'] = $seo;
        }

        $service = Service::create($createData);

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
            'imageAlt'         => 'sometimes|nullable|string|max:500',
            'altImage'         => 'sometimes|nullable|string|max:500',
            'features'         => 'sometimes|nullable|array',
            'features.*'       => 'string',
            'deliverables'     => 'sometimes|nullable|array',
            'deliverables.*'   => 'string',
            'order'            => 'sometimes|integer',
            'isFeatured'       => 'sometimes|boolean',
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
            'seo',
        ]);

        if ($request->has('imageAlt') || $request->has('altImage')) {
            $imageAlt = (string) $request->input('imageAlt', $request->input('altImage', ''));
            if (\Illuminate\Support\Facades\Schema::hasColumn('services', 'imageAlt')) {
                $data['imageAlt'] = $imageAlt;
            } else {
                $seo = isset($data['seo']) && is_array($data['seo']) ? $data['seo'] : (is_array($service->seo) ? $service->seo : []);
                $seo['imageAlt'] = $imageAlt;
                $data['seo'] = $seo;
            }
        }

        if ($request->has('isPublished')) {
            $data['isPublished'] = filter_var($request->input('isPublished'), FILTER_VALIDATE_BOOLEAN);
        }

        if ($request->has('isFeatured')) {
            $data['isFeatured'] = filter_var($request->input('isFeatured'), FILTER_VALIDATE_BOOLEAN);
        }

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

