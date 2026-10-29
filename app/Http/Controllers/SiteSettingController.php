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
        $input = $request->all();

        // [Contact Page API] If contactInfo is passed as a nested object, unpack it
        if (isset($input['contactInfo']) && is_array($input['contactInfo'])) {
            foreach ($input['contactInfo'] as $k => $v) {
                if (! array_key_exists($k, $input)) {
                    $input[$k] = $v;
                }
            }
        } elseif (isset($input['contact_info']) && is_array($input['contact_info'])) {
            foreach ($input['contact_info'] as $k => $v) {
                if (! array_key_exists($k, $input)) {
                    $input[$k] = $v;
                }
            }
        }

        // Auto-decode JSON strings if sent from string textareas
        foreach (['offices', 'socials', 'announcement'] as $jsonField) {
            if (isset($input[$jsonField]) && is_string($input[$jsonField])) {
                $decoded = json_decode($input[$jsonField], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $input[$jsonField] = $decoded;
                }
            }
        }

        $validator = validator($input, [
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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $setting = SiteSetting::getSingleton();

        $data = [];
        $fillable = [
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
        ];

        foreach ($fillable as $key) {
            if (array_key_exists($key, $input)) {
                $data[$key] = $input[$key];
            }
        }

        if (array_key_exists('map_embed_url', $input) && ! array_key_exists('mapEmbedUrl', $data)) {
            $data['mapEmbedUrl'] = $input['map_embed_url'];
        }

        $setting->update($data);
        $setting->refresh();

        return response()->json([
            'success'     => true,
            'message'     => 'Site settings updated successfully.',
            'contactInfo' => $setting->toContactInfo(),
            'data'        => $setting->toPayload(),
        ], 200);
    }
}

