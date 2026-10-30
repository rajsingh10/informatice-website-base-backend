<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FaqController extends Controller
{
    /**
     * GET /api/public/faqs & /public/faqs
     * Public list of published FAQs.
     */
    public function indexPublic(Request $request): JsonResponse
    {
        $query = Faq::published()
            ->category($request->input('category'))
            ->when($request->has('featured') || $request->has('isFeatured'), fn ($q) => $q->where('isFeatured', filter_var($request->input('featured', $request->input('isFeatured')), FILTER_VALIDATE_BOOLEAN)))
            ->search($request->input('q') ?? $request->input('search'))
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc');

        $items = $query->get()->map(fn ($f) => $f->toPublicPayload());

        return response()->json([
            'success' => true,
            'total' => $items->count(),
            'data' => $items,
        ], 200);
    }

    /**
     * GET /api/admin/faqs & /admin/faqs
     * Admin list of all FAQs.
     */
    public function indexAdmin(Request $request): JsonResponse
    {
        $query = Faq::query()
            ->category($request->input('category'))
            ->when($request->has('featured') || $request->has('isFeatured'), fn ($q) => $q->where('isFeatured', filter_var($request->input('featured', $request->input('isFeatured')), FILTER_VALIDATE_BOOLEAN)))
            ->search($request->input('q') ?? $request->input('search'))
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc');

        $items = $query->get()->map(fn ($f) => $f->toAdminPayload());

        return response()->json([
            'success' => true,
            'total' => $items->count(),
            'data' => $items,
        ], 200);
    }

    /**
     * GET /api/admin/faqs/{id} & /admin/faqs/{id}
     * Single FAQ detail for admin.
     */
    public function showAdmin(int $id): JsonResponse
    {
        $faq = Faq::find($id);

        if (! $faq) {
            return response()->json([
                'success' => false,
                'message' => 'FAQ item not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $faq->toAdminPayload(),
        ], 200);
    }

    /**
     * PUT /api/admin/faqs & /admin/faqs
     * Save the whole FAQ list (replace-all, preserves order).
     * Accepts:
     *   { "faqs": [ { "id": 1, "question": "...", "answer": "...", "category": "...", "order": 1, "isPublished": true }, ... ] }
     * OR array directly:
     *   [ { ... }, { ... } ]
     */
    public function saveAll(Request $request): JsonResponse
    {
        $items = $request->input('faqs', $request->all());

        if (! is_array($items)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid FAQs list provided. Expected an array of FAQ items.',
            ], 422);
        }

        DB::transaction(function () use ($items) {
            $processedIds = [];
            $order = 1;

            foreach ($items as $item) {
                if (! is_array($item) || empty($item['question']) || empty($item['answer'])) {
                    continue;
                }

                $faqData = [
                    'question' => (string) $item['question'],
                    'answer' => (string) $item['answer'],
                    'category' => (string) ($item['category'] ?? 'General'),
                    'order' => isset($item['order']) && is_numeric($item['order']) ? (int) $item['order'] : $order++,
                ];

                if (\Illuminate\Support\Facades\Schema::hasColumn('faqs', 'isPublished')) {
                    $faqData['isPublished'] = isset($item['isPublished']) ? (bool) $item['isPublished'] : true;
                }

                if (\Illuminate\Support\Facades\Schema::hasColumn('faqs', 'isFeatured')) {
                    $faqData['isFeatured'] = isset($item['isFeatured']) ? (bool) $item['isFeatured'] : false;
                }

                if (! empty($item['id']) && is_numeric($item['id'])) {
                    $existing = Faq::find((int) $item['id']);
                    if ($existing) {
                        $existing->update($faqData);
                        $processedIds[] = $existing->id;

                        continue;
                    }
                }

                $created = Faq::create($faqData);
                $processedIds[] = $created->id;
            }

            // Remove items omitted from replace-all payload (if payload had valid items)
            if (! empty($processedIds)) {
                Faq::whereNotIn('id', $processedIds)->delete();
            }
        });

        $faqs = Faq::orderBy('order', 'asc')->orderBy('id', 'asc')->get()->map(fn ($f) => $f->toAdminPayload());

        return response()->json([
            'success' => true,
            'message' => 'All FAQs updated successfully.',
            'total' => $faqs->count(),
            'data' => $faqs,
        ], 200);
    }

    /**
     * POST /api/admin/faqs & /admin/faqs
     * Create single FAQ item.
     */
    public function store(Request $request): JsonResponse
    {
        // If bulk array or 'faqs' key is provided in POST /admin/faqs, handle as bulk save
        if ($request->has('faqs') || (is_array($request->all()) && isset($request->all()[0]) && is_array($request->all()[0]))) {
            return $this->saveAll($request);
        }

        $validated = $request->validate(Faq::validationRules(false));

        if (! \Illuminate\Support\Facades\Schema::hasColumn('faqs', 'isPublished')) {
            unset($validated['isPublished']);
        }

        if (! \Illuminate\Support\Facades\Schema::hasColumn('faqs', 'isFeatured')) {
            unset($validated['isFeatured']);
        }

        if (! isset($validated['order'])) {
            $maxOrder = Faq::max('order') ?? 0;
            $validated['order'] = $maxOrder + 1;
        }

        $faq = Faq::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'FAQ item created successfully.',
            'data' => $faq->toAdminPayload(),
        ], 201);
    }

    /**
     * PUT /api/admin/faqs/{id} & /admin/faqs/{id}
     * Update single FAQ item.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $faq = Faq::find($id);

        if (! $faq) {
            return response()->json([
                'success' => false,
                'message' => 'FAQ item not found.',
            ], 404);
        }

        $validated = $request->validate(Faq::validationRules(true));

        if (! \Illuminate\Support\Facades\Schema::hasColumn('faqs', 'isPublished')) {
            unset($validated['isPublished']);
        }

        if (! \Illuminate\Support\Facades\Schema::hasColumn('faqs', 'isFeatured')) {
            unset($validated['isFeatured']);
        }

        $faq->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'FAQ item updated successfully.',
            'data' => $faq->toAdminPayload(),
        ], 200);
    }

    /**
     * DELETE /api/admin/faqs/{id} & /admin/faqs/{id}
     * Delete single FAQ item.
     */
    public function destroy(int $id): JsonResponse
    {
        $faq = Faq::find($id);

        if (! $faq) {
            return response()->json([
                'success' => false,
                'message' => 'FAQ item not found.',
            ], 404);
        }

        $faq->delete();

        return response()->json([
            'success' => true,
            'message' => 'FAQ item deleted successfully.',
        ], 200);
    }
}
