<?php

namespace App\Http\Controllers;

use App\Models\PageSeo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    /**
     * GET /public/seo/:page — No Auth
     * Read SEO for a page.
     */
    public function showPublic(string $page): JsonResponse
    {
        if (! PageSeo::isValidPage($page)) {
            return response()->json([
                'success' => false,
                'message' => "Invalid page '{$page}'. Must be one of: " . implode(', ', PageSeo::$validPages),
            ], 422);
        }

        $seo = PageSeo::find($page);
        $data = $seo ? $seo->toPayload() : PageSeo::defaults($page);

        return response()->json([
            'success' => true,
            'data'    => $data,
        ], 200);
    }

    /**
     * GET /admin/seo/:page — Auth Required
     * Admin read (returns empty defaults if unset).
     */
    public function showAdmin(string $page): JsonResponse
    {
        if (! PageSeo::isValidPage($page)) {
            return response()->json([
                'success' => false,
                'message' => "Invalid page '{$page}'. Must be one of: " . implode(', ', PageSeo::$validPages),
            ], 422);
        }

        $seo = PageSeo::find($page);
        $data = $seo ? $seo->toPayload() : PageSeo::defaults($page);

        return response()->json([
            'success' => true,
            'data'    => $data,
        ], 200);
    }

    /**
     * PUT /admin/seo/:page — Auth Required
     * Replace SEO for a page.
     */
    public function update(Request $request, string $page): JsonResponse
    {
        if (! PageSeo::isValidPage($page)) {
            return response()->json([
                'success' => false,
                'message' => "Invalid page '{$page}'. Must be one of: " . implode(', ', PageSeo::$validPages),
            ], 422);
        }

        $request->validate([
            'title'         => 'nullable|string|max:70',
            'description'   => 'nullable|string|max:160',
            'keywords'      => 'nullable|string',
            'ogTitle'       => 'nullable|string|max:255',
            'ogDescription' => 'nullable|string',
            'ogImage'       => 'nullable|string|max:1024',
            'canonicalUrl'  => 'nullable|string|max:1024',
            'robots'        => 'nullable|string|max:100',
            'imageAltMap'   => 'nullable|array',
        ], [
            'title.max'       => 'The title must not exceed 70 characters.',
            'description.max' => 'The description must not exceed 160 characters.',
        ]);

        $seo = PageSeo::updateOrCreate(
            ['page' => $page],
            [
                'title'         => $request->input('title', ''),
                'description'   => $request->input('description', ''),
                'keywords'      => $request->input('keywords', ''),
                'ogTitle'       => $request->input('ogTitle', ''),
                'ogDescription' => $request->input('ogDescription', ''),
                'ogImage'       => $request->input('ogImage', ''),
                'canonicalUrl'  => $request->input('canonicalUrl', ''),
                'robots'        => $request->input('robots', 'index, follow'),
                'imageAltMap'   => $request->input('imageAltMap', []),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "SEO for '{$page}' updated successfully.",
            'data'    => $seo->toPayload(),
        ], 200);
    }
}

