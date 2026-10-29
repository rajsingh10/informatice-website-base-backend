<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    /**
     * GET /public/settings & /api/public/settings — No Auth
     * Returns site-wide details.
     */
    public function show(): JsonResponse
    {
        $setting = SiteSetting::getSingleton();

        return response()->json([
            'success' => true,
            'data'    => $setting->toPayload(),
        ], 200);
    }

    /**
     * PUT /admin/settings & /api/admin/settings — Auth Required
     * Update site-wide details.
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'siteName'     => 'sometimes|nullable|string|max:255',
            'logoUrl'      => 'sometimes|nullable|string|max:1024',
            'faviconUrl'   => 'sometimes|nullable|string|max:1024',
            'email'        => 'sometimes|nullable|string|max:255',
            'phone'        => 'sometimes|nullable|string|max:50',
            'address'      => 'sometimes|nullable|string',
            'mapEmbedUrl'  => 'sometimes|nullable|string',
            'offices'      => 'sometimes|nullable|array',
            'socials'      => 'sometimes|nullable|array',
            'announcement' => 'sometimes|nullable|array',
        ]);

        $setting = SiteSetting::getSingleton();

        $data = $request->only([
            'siteName',
            'logoUrl',
            'faviconUrl',
            'email',
            'phone',
            'address',
            'mapEmbedUrl',
            'offices',
            'socials',
            'announcement',
        ]);

        $setting->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Site settings updated successfully.',
            'data'    => $setting->fresh()->toPayload(),
        ], 200);
    }
}

