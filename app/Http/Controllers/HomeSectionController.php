<?php

namespace App\Http\Controllers;

use App\Models\HomeSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeSectionController extends Controller
{
    /**
     * GET /public/home-sections — No Auth
     * Returns section visibility map for the 10 sections.
     */
    public function index(): JsonResponse
    {
        $home = HomeSection::getSingleton();

        return response()->json([
            'success' => true,
            'data'    => $home->getMap(),
        ], 200);
    }

    /**
     * PUT /admin/home-sections/batch — Auth Required
     * Save all or partial section states. Unknown section ID returns 422.
     * Body: { "sections": { "hero": true, "about": false } }
     */
    public function batch(Request $request): JsonResponse
    {
        $request->validate([
            'sections' => 'required|array',
        ], [
            'sections.required' => 'The sections field is required.',
            'sections.array'    => 'The sections field must be an object map.',
        ]);

        $incoming = $request->input('sections', []);

        // Validate every section ID
        $invalidKeys = [];
        foreach (array_keys($incoming) as $secKey) {
            if (! HomeSection::isValidSection($secKey)) {
                $invalidKeys[] = $secKey;
            }
        }

        if (! empty($invalidKeys)) {
            return response()->json([
                'success' => false,
                'message' => 'Unknown section ID(s): ' . implode(', ', $invalidKeys) . '. Must be one of: ' . implode(', ', HomeSection::$validSections),
                'errors'  => [
                    'sections' => ['Unknown section ID(s): ' . implode(', ', $invalidKeys)],
                ],
            ], 422);
        }

        $home = HomeSection::getSingleton();
        $currentMap = $home->getMap();

        foreach ($incoming as $secKey => $enabled) {
            $currentMap[$secKey] = (bool) $enabled;
        }

        $home->update(['sections' => $currentMap]);

        return response()->json([
            'success' => true,
            'message' => 'Home sections visibility updated successfully.',
            'data'    => $home->fresh()->getMap(),
        ], 200);
    }

    /**
     * POST /admin/home-sections/reset — Auth Required
     * Enable all 10 sections (all set to true).
     */
    public function reset(): JsonResponse
    {
        $home = HomeSection::getSingleton();
        $defaults = HomeSection::defaults();
        $home->update(['sections' => $defaults]);

        return response()->json([
            'success' => true,
            'message' => 'All home sections enabled.',
            'data'    => $defaults,
        ], 200);
    }

    /**
     * PUT /admin/home-sections/:sectionId — Auth Required
     * Toggle one section. Unknown section ID returns 422.
     * Body: { "enabled": false }
     */
    public function update(Request $request, string $sectionId): JsonResponse
    {
        if (! HomeSection::isValidSection($sectionId)) {
            return response()->json([
                'success' => false,
                'message' => "Unknown section ID '{$sectionId}'. Must be one of: " . implode(', ', HomeSection::$validSections),
                'errors'  => [
                    'sectionId' => ["Unknown section ID '{$sectionId}'."],
                ],
            ], 422);
        }

        $request->validate([
            'enabled' => 'required|boolean',
        ], [
            'enabled.required' => 'The enabled boolean field is required.',
            'enabled.boolean'  => 'The enabled field must be true or false.',
        ]);

        $enabled = (bool) $request->input('enabled');

        $home = HomeSection::getSingleton();
        $map = $home->getMap();
        $map[$sectionId] = $enabled;
        $home->update(['sections' => $map]);

        return response()->json([
            'success' => true,
            'message' => "Section '{$sectionId}' updated to " . ($enabled ? 'enabled' : 'disabled') . '.',
            'data'    => [
                'sectionId' => $sectionId,
                'enabled'   => $enabled,
            ],
        ], 200);
    }
}

