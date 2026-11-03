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
            'badge'                   => 'nullable|string|max:255',
            'title'                   => 'nullable|string|max:255',
            'tagline'                 => 'nullable|string',
            'storyTitle'              => 'nullable|string|max:255',
            'storyContent'            => 'nullable|string',
            'heroImage'               => 'nullable|string|max:1024',
            'heroImageAlt'            => 'nullable|string|max:255',
            'storyImage'              => 'nullable|string|max:1024',
            'storyImageAlt'           => 'nullable|string|max:255',
            'mission'                 => 'nullable|string',
            'vision'                  => 'nullable|string',
            'values'                  => 'nullable|array',
            'milestones'              => 'nullable|array',
            'stats'                   => 'nullable|array',
            'images'                  => 'nullable',
            'teamSectionEnabled'      => 'nullable|boolean',
            'differentiatorsFeatured' => 'nullable|boolean',
            'storyFeatured'           => 'nullable|boolean',
            'valuesFeatured'          => 'nullable|boolean',
            'differentiators_featured'=> 'nullable|boolean',
            'story_featured'          => 'nullable|boolean',
            'values_featured'         => 'nullable|boolean',
        ]);

        $about = AboutPage::getSingleton();

        // Handle multiple images if provided in request
        if ($request->has('images')) {
            $rawImages = $request->input('images');
            if (is_string($rawImages)) {
                $decoded = json_decode($rawImages, true);
                if (is_array($decoded)) {
                    $rawImages = $decoded;
                }
            }
            if (is_array($rawImages)) {
                $normalized = [];
                foreach ($rawImages as $idx => $item) {
                    $norm = AboutPage::normalizeImageItem($item, $idx);
                    if ($norm) {
                        $normalized[] = $norm;
                    }
                }
                $about->images = $normalized;
            }
            unset($validated['images']);
        }

        // Handle uploaded file(s) for multiple images if present
        if ($request->hasFile('images')) {
            $files = $request->file('images');
            if (! is_array($files)) {
                $files = [$files];
            }
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('uploads/about', 'public');
                    $url = asset('storage/' . $path);
                    $about->addImage(['url' => $url, 'alt' => $file->getClientOriginalName()]);
                }
            }
            unset($validated['images']);
        }

        $about->fill($validated);

        if ($request->has('differentiatorsFeatured') || $request->has('differentiators_featured')) {
            $about->differentiatorsFeatured = filter_var($request->input('differentiatorsFeatured', $request->input('differentiators_featured')), FILTER_VALIDATE_BOOLEAN);
        }
        if ($request->has('storyFeatured') || $request->has('story_featured')) {
            $about->storyFeatured = filter_var($request->input('storyFeatured', $request->input('story_featured')), FILTER_VALIDATE_BOOLEAN);
        }
        if ($request->has('valuesFeatured') || $request->has('values_featured')) {
            $about->valuesFeatured = filter_var($request->input('valuesFeatured', $request->input('values_featured')), FILTER_VALIDATE_BOOLEAN);
        }

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
     * PUT/POST /api/admin/about/images — Auth Required
     * Dedicated endpoint to update image URLs and ALT attributes for the About page.
     */
    public function updateImages(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'heroImage'     => 'nullable|string|max:1024',
            'heroImageAlt'  => 'nullable|string|max:255',
            'storyImage'    => 'nullable|string|max:1024',
            'storyImageAlt' => 'nullable|string|max:255',
            'images'        => 'nullable',
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
            $rawImages = $request->input('images');
            if (is_string($rawImages)) {
                $decoded = json_decode($rawImages, true);
                if (is_array($decoded)) {
                    $rawImages = $decoded;
                }
            }
            if (is_array($rawImages)) {
                $normalized = [];
                foreach ($rawImages as $idx => $item) {
                    $norm = AboutPage::normalizeImageItem($item, $idx);
                    if ($norm) {
                        $normalized[] = $norm;
                    }
                }
                $about->images = $normalized;
            }
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
                'images'       => $about->getNormalizedImages(),
                'imageAltMap'  => (object) $altMap,
            ],
        ], 200);
    }

    /**
     * POST /api/admin/about/images & /api/admin/about/image — Auth Required
     * Add a new image to the multiple images list.
     */
    public function addImage(Request $request): JsonResponse
    {
        $about = AboutPage::getSingleton();

        $url = $request->input('url') ?? $request->input('image') ?? '';
        $alt = $request->input('alt') ?? $request->input('imageAlt') ?? '';
        $title = $request->input('title') ?? '';
        $slot = $request->input('slot');

        // Handle file upload if present
        if ($request->hasFile('image') || $request->hasFile('file')) {
            $file = $request->file('image') ?? $request->file('file');
            if ($file && $file->isValid()) {
                $path = $file->store('uploads/about', 'public');
                $url = asset('storage/' . $path);
                if (empty($alt)) {
                    $alt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                }
            }
        }

        if (empty($url)) {
            return response()->json([
                'success' => false,
                'message' => 'Image URL or file is required to add an image.',
            ], 422);
        }

        $newImage = $about->addImage([
            'url'   => $url,
            'alt'   => $alt,
            'title' => $title,
            'slot'  => $slot,
        ]);

        // Sync to PageSeo
        $altMap = $about->getImageAltMap();
        $pageSeo = PageSeo::find('about');
        if ($pageSeo) {
            $existing = (array) ($pageSeo->imageAltMap ?: []);
            $pageSeo->imageAltMap = array_merge($existing, $altMap);
            $pageSeo->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Image added to About page successfully.',
            'data'    => [
                'image'  => $newImage,
                'images' => $about->getNormalizedImages(),
            ],
        ], 201);
    }

    /**
     * PUT/PATCH /api/admin/about/images/{identifier} — Auth Required
     * Edit an existing image in the multiple images list by id, slot, or index.
     */
    public function updateSingleImage(Request $request, string $identifier): JsonResponse
    {
        $about = AboutPage::getSingleton();

        $imageData = [];

        if ($request->has('url')) {
            $imageData['url'] = $request->input('url');
        }
        if ($request->has('alt') || $request->has('imageAlt')) {
            $imageData['alt'] = $request->input('alt', $request->input('imageAlt'));
        }
        if ($request->has('title')) {
            $imageData['title'] = $request->input('title');
        }
        if ($request->has('slot')) {
            $imageData['slot'] = $request->input('slot');
        }

        // Handle file upload if present
        if ($request->hasFile('image') || $request->hasFile('file')) {
            $file = $request->file('image') ?? $request->file('file');
            if ($file && $file->isValid()) {
                $path = $file->store('uploads/about', 'public');
                $imageData['url'] = asset('storage/' . $path);
            }
        }

        $updated = $about->updateImage($identifier, $imageData);

        if (! $updated) {
            return response()->json([
                'success' => false,
                'message' => "Image with identifier '{$identifier}' not found in About page images.",
            ], 404);
        }

        // Sync to PageSeo
        $altMap = $about->getImageAltMap();
        $pageSeo = PageSeo::find('about');
        if ($pageSeo) {
            $existing = (array) ($pageSeo->imageAltMap ?: []);
            $pageSeo->imageAltMap = array_merge($existing, $altMap);
            $pageSeo->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'About page image updated successfully.',
            'data'    => [
                'image'  => $updated,
                'images' => $about->getNormalizedImages(),
            ],
        ], 200);
    }

    /**
     * DELETE /api/admin/about/images/{identifier} — Auth Required
     * Delete an image from the multiple images list.
     */
    public function deleteSingleImage(string $identifier): JsonResponse
    {
        $about = AboutPage::getSingleton();
        $deleted = $about->deleteImage($identifier);

        if (! $deleted) {
            return response()->json([
                'success' => false,
                'message' => "Image with identifier '{$identifier}' not found in About page images.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'About page image deleted successfully.',
            'data'    => [
                'images' => $about->getNormalizedImages(),
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

