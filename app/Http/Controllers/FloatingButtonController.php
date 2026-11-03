<?php

namespace App\Http\Controllers;

use App\Models\FloatingButton;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FloatingButtonController extends Controller
{
    /**
     * GET /api/public/floating-button
     * Read published floating button settings for the public website.
     */
    public function showPublic(): JsonResponse
    {
        $button = FloatingButton::first();

        $data = $button ? $button->toPublicPayload() : FloatingButton::defaults();

        return response()->json([
            'success' => true,
            'data'    => $data,
        ], 200);
    }

    /**
     * GET /api/admin/floating-button
     * Read current floating button settings for the admin panel.
     */
    public function showAdmin(): JsonResponse
    {
        $button = FloatingButton::getSingleton();

        return response()->json([
            'success' => true,
            'data'    => $button->toPayload(),
        ], 200);
    }

    /**
     * PUT /api/admin/floating-button
     * POST /api/admin/floating-button
     * Update and save floating button settings.
     */
    public function update(Request $request): JsonResponse
    {
        $button = FloatingButton::getSingleton();

        // Normalize position input (support 'bottom-left', 'bottom-right', 'Bottom Left', 'Bottom Right', etc.)
        $positionInput = $request->input('position') 
            ?? $request->input('screenPosition') 
            ?? $request->input('screen_position') 
            ?? $button->position;

        $normalizedPosition = strtolower(trim(str_replace([' ', '_'], '-', (string) $positionInput)));
        if (! in_array($normalizedPosition, ['bottom-left', 'bottom-right'], true)) {
            $normalizedPosition = 'bottom-right';
        }

        // Resolve enabled boolean
        $enabled = $request->has('enabled')
            ? filter_var($request->input('enabled'), FILTER_VALIDATE_BOOLEAN)
            : $button->enabled;

        // Resolve button text
        $buttonText = $request->input('buttonText')
            ?? $request->input('button_text')
            ?? $request->input('text')
            ?? $button->buttonText
            ?? 'Contact us';

        // Resolve button link
        $buttonLink = $request->input('buttonLink')
            ?? $request->input('button_link')
            ?? $request->input('link')
            ?? $request->input('url')
            ?? $button->buttonLink
            ?? '/contact';

        // Resolve colors
        $buttonColor = $request->input('buttonColor')
            ?? $request->input('button_color')
            ?? $button->buttonColor
            ?? '#2563eb';

        $textColor = $request->input('textColor')
            ?? $request->input('text_color')
            ?? $button->textColor
            ?? '#ffffff';

        // Update model
        $button->update([
            'enabled'     => $enabled,
            'buttonText'  => trim((string) $buttonText),
            'buttonLink'  => trim((string) $buttonLink),
            'buttonColor' => trim((string) $buttonColor),
            'textColor'   => trim((string) $textColor),
            'position'    => $normalizedPosition,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Floating button settings saved successfully',
            'data'    => $button->fresh()->toPayload(),
        ], 200);
    }

    /**
     * POST /api/admin/floating-button/reset
     * Reset floating button to factory default settings.
     */
    public function reset(): JsonResponse
    {
        $button = FloatingButton::getSingleton();

        $button->update([
            'enabled'     => true,
            'buttonText'  => 'Contact us',
            'buttonLink'  => '/contact',
            'buttonColor' => '#2563eb',
            'textColor'   => '#ffffff',
            'position'    => 'bottom-right',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Floating button reset to defaults successfully',
            'data'    => $button->fresh()->toPayload(),
        ], 200);
    }
}

