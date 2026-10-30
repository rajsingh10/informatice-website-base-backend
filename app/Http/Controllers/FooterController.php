<?php

namespace App\Http\Controllers;

use App\Models\FooterConfig;
use App\Models\Layout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FooterController extends Controller
{
    /**
     * GET /public/footer & /api/public/footer — No Auth
     * Everything the public footer needs.
     */
    public function showPublic(): JsonResponse
    {
        $footer = FooterConfig::getSingleton();

        return response()->json([
            'success' => true,
            'data'    => $footer->toPublicPayload(),
        ], 200);
    }

    /**
     * GET /admin/footer & /api/admin/footer & /admin/modules/footer — Auth Required
     * Full admin configuration.
     */
    public function showAdmin(): JsonResponse
    {
        $footer = FooterConfig::getSingleton();

        return response()->json([
            'success' => true,
            'module'  => 'footer',
            'data'    => $footer->toAdminPayload(),
        ], 200);
    }

    /**
     * PUT/POST /admin/footer — Auth Required
     * General footer configuration save.
     */
    public function updateFooter(Request $request): JsonResponse
    {
        $footer = FooterConfig::getSingleton();

        if ($request->has('layoutId') || $request->has('templateId') || $request->has('layout')) {
            $tpl = $request->input('layoutId', $request->input('templateId', $request->input('layout')));
            if (in_array($tpl, FooterConfig::$allowedTemplates)) {
                $footer->layoutId = $tpl;
                Layout::updateOrCreate(['key' => 'footerLayout'], ['templateId' => $tpl, 'updatedAt' => now()]);
            }
        }

        if ($request->has('cta')) {
            $cta = $request->input('cta');
            if (is_array($cta)) {
                $footer->cta = [
                    'enabled' => isset($cta['enabled']) ? filter_var($cta['enabled'], FILTER_VALIDATE_BOOLEAN) : true,
                    'label'   => (string) ($cta['label'] ?? 'Schedule Consultation'),
                    'url'     => (string) ($cta['url'] ?? '/contact'),
                ];
            }
        }

        if ($request->has('grid')) {
            $grid = $request->input('grid');
            if (is_array($grid)) {
                $cols = (int) ($grid['columns'] ?? 4);
                $rows = (int) ($grid['rows'] ?? 1);
                $footer->grid = [
                    'columns' => max(1, min(4, $cols)),
                    'rows'    => max(1, min(3, $rows)),
                ];
            }
        }

        if ($request->has('items') || $request->has('links')) {
            $itemsInput = $request->input('items', $request->input('links'));
            if (is_array($itemsInput)) {
                $processed = [];
                foreach ($itemsInput as $idx => $link) {
                    if (! is_array($link)) continue;
                    $processed[] = [
                        'id'      => (string) ($link['id'] ?? 'foot-' . ($idx + 1)),
                        'label'   => (string) ($link['label'] ?? $link['title'] ?? 'Link'),
                        'url'     => (string) ($link['url'] ?? $link['href'] ?? '/'),
                        'enabled' => isset($link['enabled']) ? filter_var($link['enabled'], FILTER_VALIDATE_BOOLEAN) : true,
                    ];
                }
                $footer->links = $processed;
            }
        }

        if ($request->has('customCode')) {
            $code = $request->input('customCode');
            if (is_array($code)) {
                $footer->customCode = [
                    'enabled' => isset($code['enabled']) ? filter_var($code['enabled'], FILTER_VALIDATE_BOOLEAN) : false,
                    'html'    => (string) ($code['html'] ?? ''),
                    'css'     => (string) ($code['css'] ?? ''),
                    'js'      => (string) ($code['js'] ?? ''),
                ];
                Log::info('Footer custom code updated by admin', ['user' => $request->user()?->id ?? 'admin']);
            }
        }

        $footer->save();

        return response()->json([
            'success' => true,
            'message' => 'Footer configuration updated successfully.',
            'data'    => $footer->toAdminPayload(),
        ], 200);
    }

    /**
     * GET /admin/footer/links & /api/admin/footer/links & /admin/footer/items
     */
    public function getLinks(): JsonResponse
    {
        $footer = FooterConfig::getSingleton();
        $items = $footer->getItemsList();

        return response()->json([
            'success' => true,
            'total'   => count($items),
            'items'   => $items,
            'links'   => $items,
            'data'    => $items,
        ], 200);
    }

    /**
     * PUT/POST /admin/footer/links & /api/admin/footer/links & /admin/footer/items
     * Replace footer links/items.
     */
    public function updateLinks(Request $request): JsonResponse
    {
        $footer = FooterConfig::getSingleton();
        $linksInput = $request->input('links', $request->input('items', $request->all()));
        if (! is_array($linksInput)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid links data provided.',
            ], 422);
        }

        // If wrapped under { "links": [...] } or { "items": [...] }
        if (isset($linksInput['links']) && is_array($linksInput['links'])) {
            $linksInput = $linksInput['links'];
        } elseif (isset($linksInput['items']) && is_array($linksInput['items'])) {
            $linksInput = $linksInput['items'];
        }

        $processed = [];
        foreach ($linksInput as $idx => $link) {
            if (! is_array($link)) continue;
            $processed[] = [
                'id'      => (string) ($link['id'] ?? 'foot-' . ($idx + 1)),
                'label'   => (string) ($link['label'] ?? $link['title'] ?? 'Link'),
                'url'     => (string) ($link['url'] ?? $link['href'] ?? '/'),
                'enabled' => isset($link['enabled']) ? filter_var($link['enabled'], FILTER_VALIDATE_BOOLEAN) : true,
            ];
        }

        $footer->links = $processed;
        $footer->save();

        return response()->json([
            'success' => true,
            'message' => 'Footer links replaced successfully.',
            'total'   => count($processed),
            'items'   => $processed,
            'links'   => $processed,
            'data'    => $footer->toAdminPayload(),
        ], 200);
    }

    /**
     * GET /admin/footer/cta & /api/admin/footer/cta
     */
    public function getCta(): JsonResponse
    {
        $footer = FooterConfig::getSingleton();

        return response()->json([
            'success' => true,
            'cta'     => is_array($footer->cta) ? $footer->cta : FooterConfig::defaultCta(),
            'data'    => is_array($footer->cta) ? $footer->cta : FooterConfig::defaultCta(),
        ], 200);
    }

    /**
     * PUT/POST /admin/footer/cta & /api/admin/footer/cta — Auth Required
     * Save CTA button.
     */
    public function updateCta(Request $request): JsonResponse
    {
        $footer = FooterConfig::getSingleton();
        $existing = is_array($footer->cta) ? $footer->cta : FooterConfig::defaultCta();

        $footer->cta = [
            'enabled' => $request->has('enabled') ? filter_var($request->input('enabled'), FILTER_VALIDATE_BOOLEAN) : ($existing['enabled'] ?? true),
            'label'   => (string) ($request->input('label', $existing['label'] ?? 'Schedule Consultation')),
            'url'     => (string) ($request->input('url', $existing['url'] ?? '/contact')),
        ];

        $footer->save();

        return response()->json([
            'success' => true,
            'message' => 'Footer CTA updated successfully.',
            'data'    => $footer->cta,
        ], 200);
    }

    /**
     * GET /admin/footer/grid & /api/admin/footer/grid
     */
    public function getGrid(): JsonResponse
    {
        $footer = FooterConfig::getSingleton();

        return response()->json([
            'success' => true,
            'grid'    => is_array($footer->grid) ? $footer->grid : FooterConfig::defaultGrid(),
            'data'    => is_array($footer->grid) ? $footer->grid : FooterConfig::defaultGrid(),
        ], 200);
    }

    /**
     * PUT/POST /admin/footer/grid & /api/admin/footer/grid — Auth Required
     * Save columns and rows.
     */
    public function updateGrid(Request $request): JsonResponse
    {
        $request->validate([
            'columns' => ['required', 'integer', 'between:1,4'],
            'rows'    => ['required', 'integer', 'between:1,3'],
        ], [
            'columns.between' => 'Columns must be between 1 and 4.',
            'rows.between'    => 'Rows must be between 1 and 3.',
        ]);

        $footer = FooterConfig::getSingleton();
        $footer->grid = [
            'columns' => (int) $request->input('columns'),
            'rows'    => (int) $request->input('rows'),
        ];
        $footer->save();

        return response()->json([
            'success' => true,
            'message' => 'Footer grid updated successfully.',
            'data'    => $footer->grid,
        ], 200);
    }

    /**
     * GET /admin/footer/custom-code & /api/admin/footer/custom-code
     */
    public function getCustomCode(): JsonResponse
    {
        $footer = FooterConfig::getSingleton();
        $code = is_array($footer->customCode) ? $footer->customCode : FooterConfig::defaultCustomCode();

        return response()->json([
            'success'    => true,
            'customCode' => $code,
            'data'       => $code,
        ], 200);
    }

    /**
     * PUT/POST /admin/footer/custom-code & /api/admin/footer/custom-code — Auth Required
     * Save custom HTML/CSS/JS. Admin only, logged.
     */
    public function updateCustomCode(Request $request): JsonResponse
    {
        $footer = FooterConfig::getSingleton();
        $existing = is_array($footer->customCode) ? $footer->customCode : FooterConfig::defaultCustomCode();

        $footer->customCode = [
            'enabled' => $request->has('enabled') ? filter_var($request->input('enabled'), FILTER_VALIDATE_BOOLEAN) : ($existing['enabled'] ?? false),
            'html'    => (string) ($request->input('html', $existing['html'] ?? '')),
            'css'     => (string) ($request->input('css', $existing['css'] ?? '')),
            'js'      => (string) ($request->input('js', $existing['js'] ?? '')),
        ];

        $footer->save();

        Log::info('Footer custom code modified by admin user', [
            'ip'   => $request->ip(),
            'user' => $request->user()?->id ?? 'admin',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Footer custom code saved successfully.',
            'data'    => $footer->customCode,
        ], 200);
    }

    /**
     * POST /admin/footer/reset & /api/admin/footer/reset — Auth Required
     * Reset all footer config to defaults.
     */
    public function reset(): JsonResponse
    {
        $footer = FooterConfig::getSingleton();
        $defaults = FooterConfig::defaults();

        $footer->layoutId   = $defaults['layoutId'];
        $footer->cta        = $defaults['cta'];
        $footer->grid       = $defaults['grid'];
        $footer->links      = $defaults['links'];
        $footer->customCode = $defaults['customCode'];
        $footer->save();

        Layout::updateOrCreate(['key' => 'footerLayout'], ['templateId' => 'layout1', 'updatedAt' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Footer configuration reset to defaults.',
            'data'    => $footer->toAdminPayload(),
        ], 200);
    }
}
