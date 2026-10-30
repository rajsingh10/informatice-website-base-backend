<?php

namespace App\Http\Controllers;

use App\Models\AboutPage;
use App\Models\PageSeo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    /**
     * GET /api/public/about (also /public/about, /about) — No Auth
     * Returns public About page content, structured images with ALTs, layout, and SEO.
     */
    public function showPublic(): JsonResponse
    {
        $about = AboutPage::getSingleton();

        return response()->json([
            'success' => true,
            'data'    => $about->toPayload(),
        ], 200);
    }

    /**
     * GET /api/admin/about (also /admin/about) — Auth Required
     * Returns full About page details for admin edit forms.
     */
    public function showAdmin(): JsonResponse
    {
        $about = AboutPage::getSingleton();

        return response()->json([
            'success' => true,
            'data'    => $about->toPayload(),
        ], 200);
    }

    /**
     * PUT/POST /api/admin/about (also /admin/about) — Auth Required
     * Update About page content, stories, values, milestones, stats, and image ALT attributes.
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'badge'              => 'nullable|string|max:255',
            'title'              => 'nullable|string|max:255',
            'tagline'            => 'nullable|string',
            'storyTitle'         => 'nullable|string|max:255',
            'storyContent'       => 'nullable|string',
            'heroImage'          => 'nullable|string|max:1024',
            'heroImageAlt'       => 'nullable|string|max:255',
            'storyImage'         => 'nullable|string|max:1024',
            'storyImageAlt'      => 'nullable|string|max:255',
            'mission'            => 'nullable|string',
            'vision'             => 'nullable|string',
            'values'             => 'nullable|array',
            'milestones'         => 'nullable|array',
            'stats'              => 'nullable|array',
            'images'             => 'nullable|array',
            'teamSectionEnabled' => 'nullable|boolean',
        ]);

        $about = AboutPage::getSingleton();
        $about->fill($validated);
        $about->save();

        // Synchronize image alt attributes to PageSeo for 'about'
        $altMap = $about->getImageAltMap();
        $pageSeo = PageSeo::find('about');
        if ($pageSeo) {
            $existing = (array) ($pageSeo->imageAltMap ?: []);
            $pageSeo->imageAltMap = array_merge($existing, $altMap);
            $pageSeo->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'About page content and image ALT attributes updated successfully.',
            'data'    => $about->toPayload(),
        ], 200);
    }

    /**
     * PUT /api/admin/about/images — Auth Required
     * Dedicated endpoint to update image URLs and ALT attributes for the About page.
     */
    public function updateImages(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'heroImage'     => 'nullable|string|max:1024',
            'heroImageAlt'  => 'nullable|string|max:255',
            'storyImage'    => 'nullable|string|max:1024',
            'storyImageAlt' => 'nullable|string|max:255',
            'images'        => 'nullable|array',
            'images.*.slot' => 'required_with:images|string|max:100',
            'images.*.url'  => 'required_with:images|string|max:1024',
            'images.*.alt'  => 'nullable|string|max:255',
        ]);

        $about = AboutPage::getSingleton();

        if ($request->has('heroImage')) {
            $about->heroImage = $validated['heroImage'] ?? '';
        }
        if ($request->has('heroImageAlt')) {
            $about->heroImageAlt = $validated['heroImageAlt'] ?? '';
        }
        if ($request->has('storyImage')) {
            $about->storyImage = $validated['storyImage'] ?? '';
        }
        if ($request->has('storyImageAlt')) {
            $about->storyImageAlt = $validated['storyImageAlt'] ?? '';
        }
        if ($request->has('images')) {
            $about->images = $validated['images'] ?? [];
        }

        $about->save();

        // Synchronize to PageSeo
        $altMap = $about->getImageAltMap();
        $pageSeo = PageSeo::find('about');
        if ($pageSeo) {
            $existing = (array) ($pageSeo->imageAltMap ?: []);
            $pageSeo->imageAltMap = array_merge($existing, $altMap);
            $pageSeo->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'About page images and ALT attributes updated successfully.',
            'data'    => [
                'heroImage'    => $about->heroImage,
                'heroImageAlt' => $about->heroImageAlt,
                'storyImage'   => $about->storyImage,
                'storyImageAlt'=> $about->storyImageAlt,
                'images'       => $about->images,
                'imageAltMap'  => (object) $altMap,
            ],
        ], 200);
    }

    /**
     * POST /api/admin/about/reset — Auth Required
     * Reset About page content and image ALT attributes to defaults.
     */
    public function reset(): JsonResponse
    {
        $about = AboutPage::getSingleton();
        $about->fill(AboutPage::defaults());
        $about->save();

        return response()->json([
            'success' => true,
            'message' => 'About page reset to default content and ALT attributes.',
            'data'    => $about->toPayload(),
        ], 200);
    }
}

