<?php

namespace App\Http\Controllers;

use App\Models\HomeSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeSectionController extends Controller
{
    /**
     * GET /public/home-sections & /admin/home-sections — No Auth / Auth
     * Returns section visibility map for the 10 sections with detailed cards info.
     */
    public function index(): JsonResponse
    {
        $home = HomeSection::getSingleton();
        $map = $home->getMap();
        $activeCount = count(array_filter($map));

        return response()->json([
            'success'     => true,
            'activeCount' => $activeCount,
            'totalCount'  => count($map),
            'items'       => $home->getDetailedList(),
            'sections'    => $map,
            'data'        => array_merge($map, [
                'sections'    => $map,
                'items'       => $home->getDetailedList(),
                'activeCount' => $activeCount,
                'totalCount'  => count($map),
            ]),
        ], 200);
    }

    /**
     * POST|PUT /admin/home-sections, /admin/home-sections/{sectionId}, /admin/home-sections/toggle
     * Toggle or update section visibility ON / OFF.
     */
    public function toggle(Request $request, ?string $sectionId = null): JsonResponse
    {
        $home = HomeSection::getSingleton();
        $map = $home->getMap();

        // 1. Identify section ID if passed in route or body
        $secKey = $sectionId
            ?: $request->input('sectionId')
            ?: $request->input('id')
            ?: $request->input('key')
            ?: $request->input('section');

        // 2. If a specific section is identified
        if ($secKey && HomeSection::isValidSection($secKey)) {
            if ($request->has('enabled')) {
                $enabled = (bool) $request->input('enabled');
            } elseif ($request->has('visible')) {
                $enabled = (bool) $request->input('visible');
            } elseif ($request->has('status')) {
                $status = $request->input('status');
                $enabled = is_bool($status) ? $status : in_array($status, ['visible', 'on', 'true', '1', 1], true);
            } else {
                // Toggle current state
                $enabled = ! ($map[$secKey] ?? true);
            }

            $map[$secKey] = $enabled;
            $home->update(['sections' => $map]);
            $activeCount = count(array_filter($map));

            return response()->json([
                'success'     => true,
                'message'     => "Section '{$secKey}' " . ($enabled ? 'enabled (visible)' : 'disabled (hidden)') . '.',
                'section'     => $secKey,
                'sectionId'   => $secKey,
                'enabled'     => $enabled,
                'visible'     => $enabled,
                'activeCount' => $activeCount,
                'totalCount'  => count($map),
                'sections'    => $map,
                'data'        => array_merge($map, [
                    'section'     => $secKey,
                    'sectionId'   => $secKey,
                    'enabled'     => $enabled,
                    'sections'    => $map,
                    'items'       => $home->getDetailedList(),
                    'activeCount' => $activeCount,
                    'totalCount'  => count($map),
                ]),
            ], 200);
        }

        // 3. If batch update or map passed
        $incoming = $request->input('sections', $request->all());
        if (is_array($incoming) && ! empty($incoming)) {
            $updated = 0;
            foreach ($incoming as $k => $v) {
                if (HomeSection::isValidSection($k)) {
                    $map[$k] = (bool) $v;
                    $updated++;
                }
            }

            if ($updated > 0) {
                $home->update(['sections' => $map]);
                $activeCount = count(array_filter($map));

                return response()->json([
                    'success'     => true,
                    'message'     => "Home sections updated successfully ({$updated} sections changed).",
                    'activeCount' => $activeCount,
                    'totalCount'  => count($map),
                    'sections'    => $map,
                    'data'        => array_merge($map, [
                        'sections'    => $map,
                        'items'       => $home->getDetailedList(),
                        'activeCount' => $activeCount,
                        'totalCount'  => count($map),
                    ]),
                ], 200);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Please provide a valid sectionId (e.g. hero, about, services, portfolio, stats, clients, testimonials, blog, faq, cta) and enabled boolean.',
            'errors'  => [
                'sectionId' => ['Allowed: ' . implode(', ', HomeSection::$validSections)],
            ],
        ], 422);
    }

    /**
     * PUT|POST /admin/home-sections/batch — Auth Required
     * Save all or partial section states.
     */
    public function batch(Request $request): JsonResponse
    {
        return $this->toggle($request);
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
            'success'     => true,
            'message'     => 'All 10 home sections enabled (visible).',
            'activeCount' => 10,
            'totalCount'  => 10,
            'sections'    => $defaults,
            'data'        => array_merge($defaults, [
                'sections'    => $defaults,
                'items'       => $home->getDetailedList(),
                'activeCount' => 10,
                'totalCount'  => 10,
            ]),
        ], 200);
    }

    /**
     * PUT|POST /admin/home-sections/:sectionId — Auth Required
     * Toggle or set one section.
     */
    public function update(Request $request, string $sectionId): JsonResponse
    {
        return $this->toggle($request, $sectionId);
    }
}

