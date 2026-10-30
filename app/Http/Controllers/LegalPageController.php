<?php

namespace App\Http\Controllers;

use App\Models\LegalPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LegalPageController extends Controller
{
    /**
     * Normalize type to valid key.
     */
    private function normalizeType(string $type): ?string
    {
        $normalized = strtolower(trim($type));
        if ($normalized === 'terms' || $normalized === 'terms-of-service' || $normalized === 'term') {
            return 'terms';
        }
        if ($normalized === 'privacy' || $normalized === 'privacy-policy') {
            return 'privacy';
        }

        return null;
    }

    /**
     * GET /api/public/terms & /public/terms alias
     */
    public function showTermsPublic(): JsonResponse
    {
        return $this->showPublic('terms');
    }

    /**
     * GET /api/public/privacy & /public/privacy alias
     */
    public function showPrivacyPublic(): JsonResponse
    {
        return $this->showPublic('privacy');
    }

    /**
     * GET /api/admin/terms & /admin/terms alias
     */
    public function showTermsAdmin(): JsonResponse
    {
        return $this->showAdmin('terms');
    }

    /**
     * GET /api/admin/privacy & /admin/privacy alias
     */
    public function showPrivacyAdmin(): JsonResponse
    {
        return $this->showAdmin('privacy');
    }

    /**
     * PUT/POST /api/admin/terms & /admin/terms alias
     */
    public function updateTerms(Request $request): JsonResponse
    {
        return $this->update($request, 'terms');
    }

    /**
     * PUT/POST /api/admin/privacy & /admin/privacy alias
     */
    public function updatePrivacy(Request $request): JsonResponse
    {
        return $this->update($request, 'privacy');
    }

    /**
     * Default legal page titles and placeholder text.
     */
    private function defaultFor(string $type): array
    {
        if ($type === 'terms') {
            return [
                'title' => 'Terms of Service',
                'content' => '<h2>1. Acceptance of Terms</h2><p>By accessing or using our services, you agree to be bound by these Terms of Service.</p><h2>2. Intellectual Property</h2><p>All content and software are protected by international copyright laws.</p><h2>3. Limitation of Liability</h2><p>We are not liable for any indirect or consequential damages.</p>',
                'lastUpdated' => now()->format('F Y'),
            ];
        }

        return [
            'title' => 'Privacy Policy',
            'content' => '<h2>1. Information We Collect</h2><p>We collect information you provide directly to us when filling out contact forms or requesting proposals.</p><h2>2. How We Use Information</h2><p>We use information to provide, maintain, and improve our technology services.</p><h2>3. Security</h2><p>We employ enterprise-grade security controls to protect your data.</p>',
            'lastUpdated' => now()->format('F Y'),
        ];
    }

    /**
     * GET /api/public/legal/{type} & /public/legal/{type}
     * Also supports aliases: /public/terms, /public/privacy
     */
    public function showPublic(string $type): JsonResponse
    {
        $normType = $this->normalizeType($type);

        if (! $normType) {
            return response()->json([
                'success' => false,
                'message' => "Invalid legal page type '{$type}'. Allowed: terms, privacy.",
            ], 404);
        }

        $defaults = $this->defaultFor($normType);
        $page = LegalPage::firstOrCreate(
            ['type' => $normType],
            [
                'title' => $defaults['title'],
                'content' => $defaults['content'],
                'lastUpdated' => $defaults['lastUpdated'],
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $page->toPayload(),
        ], 200);
    }

    /**
     * GET /api/admin/legal/{type} & /admin/legal/{type}
     */
    public function showAdmin(string $type): JsonResponse
    {
        $normType = $this->normalizeType($type);

        if (! $normType) {
            return response()->json([
                'success' => false,
                'message' => "Invalid legal page type '{$type}'. Allowed: terms, privacy.",
            ], 404);
        }

        $defaults = $this->defaultFor($normType);
        $page = LegalPage::firstOrCreate(
            ['type' => $normType],
            [
                'title' => $defaults['title'],
                'content' => $defaults['content'],
                'lastUpdated' => $defaults['lastUpdated'],
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $page->toPayload(),
        ], 200);
    }

    /**
     * PUT /api/admin/legal/{type} & /admin/legal/{type}
     * Update legal page content.
     */
    public function update(Request $request, string $type): JsonResponse
    {
        $normType = $this->normalizeType($type);

        if (! $normType) {
            return response()->json([
                'success' => false,
                'message' => "Invalid legal page type '{$type}'. Allowed: terms, privacy.",
            ], 422);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'lastUpdated' => 'nullable|string|max:100',
        ]);

        $defaults = $this->defaultFor($normType);
        $page = LegalPage::firstOrCreate(
            ['type' => $normType],
            [
                'title' => $defaults['title'],
                'content' => $defaults['content'],
                'lastUpdated' => $defaults['lastUpdated'],
            ]
        );

        if (empty($validated['lastUpdated']) && ! isset($validated['lastUpdated'])) {
            $validated['lastUpdated'] = now()->format('F Y');
        }

        $page->update($validated);

        return response()->json([
            'success' => true,
            'message' => ucfirst($normType).' page updated successfully.',
            'data' => $page->toPayload(),
        ], 200);
    }
}
