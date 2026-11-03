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

        // Unpack nested grouped theme & styling objects if sent as objects
        $groupMaps = [
            'spacing'     => ['outerMargin', 'paddingX', 'paddingY', 'borderRadius', 'cardRadius'],
            'textDesign'  => ['fontFamilyHeading', 'fontFamilyBody', 'fontSizeBase', 'fontWeightHeading', 'fontWeightBody'],
            'buttonStyle' => ['buttonRadius', 'buttonPaddingX', 'buttonPaddingY', 'buttonShadow', 'buttonHoverAnimation'],
            'advanced'    => ['customHeaderCode', 'customFooterCode', 'googleAnalyticsId'],
            'branding'    => ['primaryColor', 'secondaryColor', 'accentColor', 'textColor', 'backgroundColor'],
        ];

        foreach ($groupMaps as $groupKey => $subKeys) {
            if (isset($input[$groupKey])) {
                $groupVal = $input[$groupKey];
                if (is_string($groupVal)) {
                    $decoded = json_decode($groupVal, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $groupVal = $decoded;
                        $input[$groupKey] = $decoded;
                    }
                }
                if (is_array($groupVal)) {
                    foreach ($subKeys as $sk) {
                        if (array_key_exists($sk, $groupVal) && ! array_key_exists($sk, $input)) {
                            $input[$sk] = $groupVal[$sk];
                        }
                    }
                }
            }
        }

        // Auto-decode JSON strings if sent from string textareas
        foreach (['offices', 'socials', 'announcement', 'spacing', 'textDesign', 'buttonStyle', 'advanced', 'branding'] as $jsonField) {
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

            // 1. Spacing
            'outerMargin'  => 'sometimes|nullable|string|max:255',
            'paddingX'     => 'sometimes|nullable|string|max:255',
            'paddingY'     => 'sometimes|nullable|string|max:255',
            'borderRadius' => 'sometimes|nullable|string|max:255',
            'cardRadius'   => 'sometimes|nullable|string|max:255',

            // 2. Text design
            'fontFamilyHeading' => 'sometimes|nullable|string|max:255',
            'fontFamilyBody'    => 'sometimes|nullable|string|max:255',
            'fontSizeBase'      => 'sometimes|nullable|string|max:255',
            'fontWeightHeading' => 'sometimes|nullable|string|max:255',
            'fontWeightBody'    => 'sometimes|nullable|string|max:255',

            // 3. Button style
            'buttonRadius'         => 'sometimes|nullable|string|max:255',
            'buttonPaddingX'        => 'sometimes|nullable|string|max:255',
            'buttonPaddingY'        => 'sometimes|nullable|string|max:255',
            'buttonShadow'         => 'sometimes|nullable|string|max:255',
            'buttonHoverAnimation' => 'sometimes|nullable|string|max:255',

            // 4. Advanced
            'customHeaderCode'  => 'sometimes|nullable|string',
            'customFooterCode'  => 'sometimes|nullable|string',
            'googleAnalyticsId' => 'sometimes|nullable|string|max:255',

            // 5. Branding
            'primaryColor'    => 'sometimes|nullable|string|max:255',
            'secondaryColor'  => 'sometimes|nullable|string|max:255',
            'accentColor'     => 'sometimes|nullable|string|max:255',
            'textColor'       => 'sometimes|nullable|string|max:255',
            'backgroundColor' => 'sometimes|nullable|string|max:255',

            // Header / Logo
            'logoPosition'  => 'sometimes|nullable|string|max:50',
            'logoAlignment' => 'sometimes|nullable|string|max:50',
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
            // Flat individual fields
            'outerMargin',
            'paddingX',
            'paddingY',
            'borderRadius',
            'cardRadius',
            'fontFamilyHeading',
            'fontFamilyBody',
            'fontSizeBase',
            'fontWeightHeading',
            'fontWeightBody',
            'buttonRadius',
            'buttonPaddingX',
            'buttonPaddingY',
            'buttonShadow',
            'buttonHoverAnimation',
            'customHeaderCode',
            'customFooterCode',
            'googleAnalyticsId',
            'primaryColor',
            'secondaryColor',
            'accentColor',
            'textColor',
            'backgroundColor',
            'logoPosition',
            'logoAlignment',
        ];

        foreach ($fillable as $key) {
            if (array_key_exists($key, $input)) {
                $data[$key] = $input[$key];
            }
        }

        if (array_key_exists('map_embed_url', $input) && ! array_key_exists('mapEmbedUrl', $data)) {
            $data['mapEmbedUrl'] = $input['map_embed_url'];
        }

        // Sync grouped JSON columns
        $existingSpacing = is_array($setting->spacing) ? $setting->spacing : [];
        $data['spacing'] = [
            'outerMargin'  => $data['outerMargin'] ?? ($existingSpacing['outerMargin'] ?? '0 auto'),
            'paddingX'     => $data['paddingX'] ?? ($existingSpacing['paddingX'] ?? '1.5rem'),
            'paddingY'     => $data['paddingY'] ?? ($existingSpacing['paddingY'] ?? '2rem'),
            'borderRadius' => $data['borderRadius'] ?? ($existingSpacing['borderRadius'] ?? '0.5rem'),
            'cardRadius'   => $data['cardRadius'] ?? ($existingSpacing['cardRadius'] ?? '0.75rem'),
        ];

        $existingText = is_array($setting->textDesign) ? $setting->textDesign : [];
        $data['textDesign'] = [
            'fontFamilyHeading' => $data['fontFamilyHeading'] ?? ($existingText['fontFamilyHeading'] ?? 'Inter, sans-serif'),
            'fontFamilyBody'    => $data['fontFamilyBody'] ?? ($existingText['fontFamilyBody'] ?? 'Inter, sans-serif'),
            'fontSizeBase'      => $data['fontSizeBase'] ?? ($existingText['fontSizeBase'] ?? '16px'),
            'fontWeightHeading' => $data['fontWeightHeading'] ?? ($existingText['fontWeightHeading'] ?? '700'),
            'fontWeightBody'    => $data['fontWeightBody'] ?? ($existingText['fontWeightBody'] ?? '400'),
        ];

        $existingButton = is_array($setting->buttonStyle) ? $setting->buttonStyle : [];
        $data['buttonStyle'] = [
            'buttonRadius'         => $data['buttonRadius'] ?? ($existingButton['buttonRadius'] ?? '0.5rem'),
            'buttonPaddingX'        => $data['buttonPaddingX'] ?? ($existingButton['buttonPaddingX'] ?? '1.25rem'),
            'buttonPaddingY'        => $data['buttonPaddingY'] ?? ($existingButton['buttonPaddingY'] ?? '0.625rem'),
            'buttonShadow'         => $data['buttonShadow'] ?? ($existingButton['buttonShadow'] ?? '0 1px 2px 0 rgba(0, 0, 0, 0.05)'),
            'buttonHoverAnimation' => $data['buttonHoverAnimation'] ?? ($existingButton['buttonHoverAnimation'] ?? 'translate-y'),
        ];

        $existingAdv = is_array($setting->advanced) ? $setting->advanced : [];
        $data['advanced'] = [
            'customHeaderCode'  => $data['customHeaderCode'] ?? ($existingAdv['customHeaderCode'] ?? ''),
            'customFooterCode'  => $data['customFooterCode'] ?? ($existingAdv['customFooterCode'] ?? ''),
            'googleAnalyticsId' => $data['googleAnalyticsId'] ?? ($existingAdv['googleAnalyticsId'] ?? ''),
        ];

        $existingBrand = is_array($setting->branding) ? $setting->branding : [];
        $data['branding'] = [
            'primaryColor'    => $data['primaryColor'] ?? ($existingBrand['primaryColor'] ?? '#2563eb'),
            'secondaryColor'  => $data['secondaryColor'] ?? ($existingBrand['secondaryColor'] ?? '#1e40af'),
            'accentColor'     => $data['accentColor'] ?? ($existingBrand['accentColor'] ?? '#38bdf8'),
            'textColor'       => $data['textColor'] ?? ($existingBrand['textColor'] ?? '#1e293b'),
            'backgroundColor' => $data['backgroundColor'] ?? ($existingBrand['backgroundColor'] ?? '#ffffff'),
        ];

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

