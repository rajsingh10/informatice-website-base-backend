<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestimonialController extends Controller
{
    /**
     * Public list of testimonials and reviews.
     * Supports ?featured=1|0, ?rating=, ?limit=.
     * Only returns published reviews sorted by order asc, id asc.
     */
    public function indexPublic(Request $request): JsonResponse
    {
        $query = Testimonial::published();

        if ($request->has('featured') || $request->has('isFeatured')) {
            $featured = $request->query('featured', $request->query('isFeatured'));
            $query->featured(filter_var($featured, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
        }

        if ($request->filled('rating')) {
            $query->where('rating', (int) $request->query('rating'));
        }

        if ($request->filled('minRating')) {
            $query->minRating((int) $request->query('minRating'));
        }

        if ($request->filled('search')) {
            $search = '%'.$request->query('search').'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('company', 'like', $search)
                    ->orWhere('designation', 'like', $search)
                    ->orWhere('text', 'like', $search);
            });
        }

        $total = $query->count();
        $query->orderBy('order', 'asc')->orderBy('id', 'asc');

        if ($request->filled('limit')) {
            $limit = max(1, (int) $request->query('limit', 20));
            $page = max(1, (int) $request->query('page', 1));
            $reviews = $query->forPage($page, $limit)->get();
        } else {
            $reviews = $query->get();
        }

        return response()->json([
            'success' => true,
            'total' => $total,
            'data' => $reviews->map(fn ($r) => $r->toPublicPayload()),
        ], 200);
    }

    /**
     * Public single testimonial by id.
     */
    public function showPublic(int $id): JsonResponse
    {
        $testimonial = Testimonial::published()->find($id);

        if (! $testimonial) {
            return response()->json([
                'success' => false,
                'message' => 'Testimonial not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $testimonial->toPublicPayload(),
        ], 200);
    }

    /**
     * Admin list of all testimonials.
     * Returns both published and unpublished reviews.
     */
    public function indexAdmin(Request $request): JsonResponse
    {
        $query = Testimonial::query();

        if ($request->has('featured') || $request->has('isFeatured')) {
            $featured = $request->query('featured', $request->query('isFeatured'));
            $query->featured(filter_var($featured, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
        }

        if ($request->has('isPublished')) {
            $isPublished = filter_var($request->query('isPublished'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($isPublished !== null) {
                $query->where('isPublished', $isPublished);
            }
        }

        if ($request->filled('rating')) {
            $query->where('rating', (int) $request->query('rating'));
        }

        if ($request->filled('search')) {
            $search = '%'.$request->query('search').'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('company', 'like', $search)
                    ->orWhere('designation', 'like', $search)
                    ->orWhere('text', 'like', $search);
            });
        }

        $total = $query->count();
        $query->orderBy('order', 'asc')->orderBy('id', 'asc');

        if ($request->filled('limit')) {
            $limit = max(1, (int) $request->query('limit', 20));
            $page = max(1, (int) $request->query('page', 1));
            $reviews = $query->forPage($page, $limit)->get();
        } else {
            $reviews = $query->get();
        }

        return response()->json([
            'success' => true,
            'total' => $total,
            'data' => $reviews->map(fn ($r) => $r->toAdminPayload()),
        ], 200);
    }

    /**
     * Admin single testimonial by id.
     */
    public function showAdmin(int $id): JsonResponse
    {
        $testimonial = Testimonial::find($id);

        if (! $testimonial) {
            return response()->json([
                'success' => false,
                'message' => 'Testimonial not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $testimonial->toAdminPayload(),
        ], 200);
    }

    /**
     * Admin create a new testimonial.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), Testimonial::validationRules(false));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if (! isset($data['order']) || $data['order'] === null) {
            $maxOrder = Testimonial::max('order');
            $data['order'] = $maxOrder !== null ? $maxOrder + 1 : 1;
        }

        if (! isset($data['rating']) || $data['rating'] === null) {
            $data['rating'] = 5;
        }

        if (! isset($data['isFeatured'])) {
            $data['isFeatured'] = false;
        }

        if (! isset($data['isPublished'])) {
            $data['isPublished'] = true;
        }

        if (empty($data['metric']) || ! is_array($data['metric'])) {
            $data['metric'] = Testimonial::defaultMetric();
        }

        $testimonial = Testimonial::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Testimonial created successfully.',
            'data' => $testimonial->toAdminPayload(),
        ], 201);
    }

    /**
     * Admin update testimonial.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $testimonial = Testimonial::find($id);

        if (! $testimonial) {
            return response()->json([
                'success' => false,
                'message' => 'Testimonial not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), Testimonial::validationRules(true));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if (array_key_exists('metric', $data) && is_array($data['metric'])) {
            $existing = is_array($testimonial->metric) ? $testimonial->metric : Testimonial::defaultMetric();
            $data['metric'] = array_merge($existing, $data['metric']);
        }

        $testimonial->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Testimonial updated successfully.',
            'data' => $testimonial->fresh()->toAdminPayload(),
        ], 200);
    }

    /**
     * Admin delete testimonial.
     */
    public function destroy(int $id): JsonResponse
    {
        $testimonial = Testimonial::find($id);

        if (! $testimonial) {
            return response()->json([
                'success' => false,
                'message' => 'Testimonial not found.',
            ], 404);
        }

        $testimonial->delete();

        return response()->json([
            'success' => true,
            'message' => 'Testimonial deleted successfully.',
        ], 200);
    }
}
