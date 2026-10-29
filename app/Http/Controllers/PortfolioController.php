<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    /**
     * GET /public/portfolio — No Auth
     * Catalog list (supports ?category=, ?search=, ?limit=, ?page=).
     * Returns: id, title, slug, client, year, category, technologies, shortDescription, image, isFeatured plus total.
     */
    public function indexPublic(Request $request): JsonResponse
    {
        $query = Portfolio::where('isPublished', true);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        // Filter featured projects
        if ($request->has('featured')) {
            $query->where('isFeatured', filter_var($request->query('featured'), FILTER_VALIDATE_BOOLEAN));
        }

        // Search in title, client, or shortDescription
        if ($request->filled('search')) {
            $term = '%' . $request->query('search') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('client', 'like', $term)
                  ->orWhere('shortDescription', 'like', $term);
            });
        }

        $total = $query->count();

        $limit = max(1, min(100, (int) $request->query('limit', 20)));
        $page = max(1, (int) $request->query('page', 1));
        $offset = ($page - 1) * $limit;

        $projects = $query->orderBy('order', 'asc')
                          ->orderBy('id', 'asc')
                          ->skip($offset)
                          ->take($limit)
                          ->get();

        return response()->json([
            'success' => true,
            'total'   => $total,
            'page'    => $page,
            'limit'   => $limit,
            'data'    => $projects->map->toListPayload(),
        ], 200);
    }

    /**
     * GET /public/portfolio/:slug — No Auth
     * Returns the full model for a single published case study.
     */
    public function showPublic(string $slug): JsonResponse
    {
        $project = Portfolio::where('slug', $slug)
                            ->where('isPublished', true)
                            ->first();

        if (! $project) {
            return response()->json([
                'success' => false,
                'message' => "Case study '{$slug}' not found.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $project->toDetailPayload(),
        ], 200);
    }

    /**
     * GET /admin/portfolio — Auth Required
     * Admin list including unpublished projects.
     */
    public function indexAdmin(Request $request): JsonResponse
    {
        $query = Portfolio::query();

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
                  ->orWhere('client', 'like', $term);
            });
        }

        $total = $query->count();
        $projects = $query->orderBy('order', 'asc')
                          ->orderBy('id', 'asc')
                          ->get();

        return response()->json([
            'success' => true,
            'total'   => $total,
            'data'    => $projects->map->toAdminPayload(),
        ], 200);
    }

    /**
     * GET /admin/portfolio/:id — Auth Required
     * Admin single record for edit form.
     */
    public function showAdmin(int|string $id): JsonResponse
    {
        $project = Portfolio::find($id);

        if (! $project) {
            return response()->json([
                'success' => false,
                'message' => "Project with ID {$id} not found.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $project->toAdminPayload(),
        ], 200);
    }

    /**
     * POST /admin/portfolio — Auth Required
     * Create project. Required: title.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'sometimes|nullable|string|max:255|unique:portfolios,slug',
            'client'           => 'sometimes|nullable|string|max:255',
            'year'             => 'sometimes|nullable|string|max:50',
            'category'         => 'sometimes|nullable|string|max:100',
            'technologies'     => 'sometimes|nullable|array',
            'technologies.*'   => 'string',
            'shortDescription' => 'sometimes|nullable|string',
            'longDescription'  => 'sometimes|nullable|string',
            'challenge'        => 'sometimes|nullable|string',
            'solution'         => 'sometimes|nullable|string',
            'results'          => 'sometimes|nullable|array',
            'image'            => 'sometimes|nullable|string|max:1024',
            'gallery'          => 'sometimes|nullable|array',
            'gallery.*'        => 'string',
            'projectUrl'       => 'sometimes|nullable|string|max:1024',
            'isFeatured'       => 'sometimes|boolean',
            'order'            => 'sometimes|integer',
            'isPublished'      => 'sometimes|boolean',
            'seo'              => 'sometimes|nullable|array',
        ], [
            'title.required' => 'The title field is required.',
            'slug.unique'    => 'This slug is already taken. Please provide a unique slug or leave it blank.',
        ]);

        $slug = $request->filled('slug')
            ? Portfolio::generateSlug($request->input('slug'))
            : Portfolio::generateSlug($request->input('title'));

        $project = Portfolio::create([
            'title'            => $request->input('title'),
            'slug'             => $slug,
            'client'           => $request->input('client', ''),
            'year'             => (string) $request->input('year', date('Y')),
            'category'         => $request->input('category', 'General'),
            'technologies'     => $request->input('technologies', []),
            'shortDescription' => $request->input('shortDescription', ''),
            'longDescription'  => $request->input('longDescription', ''),
            'challenge'        => $request->input('challenge', ''),
            'solution'         => $request->input('solution', ''),
            'results'          => $request->input('results', []),
            'image'            => $request->input('image', ''),
            'gallery'          => $request->input('gallery', []),
            'projectUrl'       => $request->input('projectUrl', ''),
            'isFeatured'       => $request->has('isFeatured') ? (bool) $request->input('isFeatured') : false,
            'order'            => (int) $request->input('order', 0),
            'isPublished'      => $request->has('isPublished') ? (bool) $request->input('isPublished') : true,
            'seo'              => $request->input('seo', []),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully.',
            'data'    => $project->toAdminPayload(),
        ], 201);
    }

    /**
     * PUT /admin/portfolio/:id — Auth Required
     * Update an existing project.
     */
    public function update(Request $request, int|string $id): JsonResponse
    {
        $project = Portfolio::find($id);

        if (! $project) {
            return response()->json([
                'success' => false,
                'message' => "Project with ID {$id} not found.",
            ], 404);
        }

        $request->validate([
            'title'            => 'sometimes|required|string|max:255',
            'slug'             => 'sometimes|required|string|max:255|unique:portfolios,slug,' . $project->id,
            'client'           => 'sometimes|nullable|string|max:255',
            'year'             => 'sometimes|nullable|string|max:50',
            'category'         => 'sometimes|nullable|string|max:100',
            'technologies'     => 'sometimes|nullable|array',
            'technologies.*'   => 'string',
            'shortDescription' => 'sometimes|nullable|string',
            'longDescription'  => 'sometimes|nullable|string',
            'challenge'        => 'sometimes|nullable|string',
            'solution'         => 'sometimes|nullable|string',
            'results'          => 'sometimes|nullable|array',
            'image'            => 'sometimes|nullable|string|max:1024',
            'gallery'          => 'sometimes|nullable|array',
            'gallery.*'        => 'string',
            'projectUrl'       => 'sometimes|nullable|string|max:1024',
            'isFeatured'       => 'sometimes|boolean',
            'order'            => 'sometimes|integer',
            'isPublished'      => 'sometimes|boolean',
            'seo'              => 'sometimes|nullable|array',
        ]);

        $data = $request->only([
            'title',
            'client',
            'year',
            'category',
            'technologies',
            'shortDescription',
            'longDescription',
            'challenge',
            'solution',
            'results',
            'image',
            'gallery',
            'projectUrl',
            'isFeatured',
            'order',
            'isPublished',
            'seo',
        ]);

        if ($request->filled('slug')) {
            $data['slug'] = Portfolio::generateSlug($request->input('slug'), $project->id);
        } elseif ($request->filled('title') && ! $project->slug) {
            $data['slug'] = Portfolio::generateSlug($request->input('title'), $project->id);
        }

        $project->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully.',
            'data'    => $project->fresh()->toAdminPayload(),
        ], 200);
    }

    /**
     * DELETE /admin/portfolio/:id — Auth Required
     * Delete a project.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $project = Portfolio::find($id);

        if (! $project) {
            return response()->json([
                'success' => false,
                'message' => "Project with ID {$id} not found.",
            ], 404);
        }

        $project->delete();

        return response()->json([
            'success' => true,
            'message' => "Project '{$project->title}' deleted successfully.",
        ], 200);
    }
}

