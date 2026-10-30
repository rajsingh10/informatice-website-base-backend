<?php

namespace App\Http\Controllers;

use App\Models\FooterConfig;
use App\Models\Layout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FooterController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────
    // GET /api/public/footer  (& /public/footer, /footer)
    // No Auth — published footer configuration.
    // ─────────────────────────────────────────────────────────────────────
    public function showPublic(): JsonResponse
    {
        $footer = FooterConfig::getSingleton();

        return response()->json([
            'success' => true,
            'data'    => $footer->toPublicPayload(),
        ], 200);
    }

    // ─────────────────────────────────────────────────────────────────────
    // GET /api/admin/footer  (& /admin/footer, /admin/modules/footer)
    // Auth Required — full admin configuration.
    // ─────────────────────────────────────────────────────────────────────
    public function showAdmin(): JsonResponse
    {
        $footer = FooterConfig::getSingleton();

        return response()->json([
            'success' => true,
            'module'  => 'footer',
            'data'    => $footer->toAdminPayload(),
        ], 200);
    }

    // ─────────────────────────────────────────────────────────────────────
    // PUT/POST /api/admin/footer — Auth Required
    // Save the complete footer configuration from Admin.
    // Supports:  links (nested submenu), grid (columns, rows, sections),
    //            cta (enabled, title, subtitle, label, url),
    //            customCode (enabled, html, css, js), layoutId / templateId.
    // Returns the saved canonical configuration.
    // ─────────────────────────────────────────────────────────────────────
    public function updateFooter(Request $request): JsonResponse
    {
        $footer = FooterConfig::getSingleton();

        // ── 1. Layout / Template ──────────────────────────────────────────
        if ($request->hasAny(['layoutId', 'templateId', 'layout'])) {
            $tpl = $request->input('layoutId')
                ?? $request->input('templateId')
                ?? $request->input('layout');

            if (in_array($tpl, FooterConfig::$allowedTemplates, true)) {
                $footer->layoutId = $tpl;
                Layout::updateOrCreate(
                    ['key' => 'footerLayout'],
                    ['templateId' => $tpl, 'updatedAt' => now()]
                );
            }
        }

        // ── 2. CTA ───────────────────────────────────────────────────────
        if ($request->has('cta')) {
            $cta = $request->input('cta');
            if (is_array($cta)) {
                $existing = is_array($footer->cta) ? $footer->cta : FooterConfig::defaultCta();
                $footer->cta = [
                    'enabled'  => isset($cta['enabled'])  ? (bool) filter_var($cta['enabled'],  FILTER_VALIDATE_BOOLEAN) : (bool) ($existing['enabled']  ?? true),
                    'title'    => (string) ($cta['title']    ?? $existing['title']    ?? ''),
                    'subtitle' => (string) ($cta['subtitle'] ?? $existing['subtitle'] ?? ''),
                    'label'    => (string) ($cta['label']    ?? $existing['label']    ?? 'Schedule Consultation'),
                    'url'      => (string) ($cta['url']      ?? $existing['url']      ?? '/contact'),
                ];
            }
        }

        // ── 3. Grid + Sections ───────────────────────────────────────────
        if ($request->has('grid')) {
            $grid     = $request->input('grid');
            $existing = is_array($footer->grid) ? $footer->grid : FooterConfig::defaultGrid();

            if (is_array($grid)) {
                $cols = (int) ($grid['columns'] ?? $existing['columns'] ?? 4);
                $rows = (int) ($grid['rows']    ?? $existing['rows']    ?? 1);
                $cols = max(1, min(4, $cols));
                $rows = max(1, min(3, $rows));

                $footer->grid = ['columns' => $cols, 'rows' => $rows];

                // Handle sections inside the grid object
                if (isset($grid['sections']) && is_array($grid['sections'])) {
                    $validation = FooterConfig::validateSections($grid['sections'], $cols, $rows);
                    if (! $validation['ok']) {
                        return response()->json([
                            'success' => false,
                            'message' => $validation['error'],
                            'errors'  => ['grid.sections' => [$validation['error']]],
                        ], 422);
                    }
                    $footer->sections = array_values($grid['sections']);
                }
            }
        }

        // Sections can also be passed at the top-level
        if ($request->has('sections') && ! ($request->has('grid') && isset($request->input('grid')['sections']))) {
            $sections  = $request->input('sections');
            $grid      = is_array($footer->grid) ? $footer->grid : FooterConfig::defaultGrid();
            $cols      = (int) ($grid['columns'] ?? 4);
            $rows      = (int) ($grid['rows']    ?? 1);

            if (is_array($sections)) {
                $validation = FooterConfig::validateSections($sections, $cols, $rows);
                if (! $validation['ok']) {
                    return response()->json([
                        'success' => false,
                        'message' => $validation['error'],
                        'errors'  => ['sections' => [$validation['error']]],
                    ], 422);
                }
                $footer->sections = array_values($sections);
            }
        }

        // ── 4. Links (with nested submenu) ────────────────────────────────
        if ($request->has('links') || $request->has('items')) {
            $raw = $request->input('links', $request->input('items'));
            if (is_array($raw)) {
                $footer->links = FooterConfig::normalizeLinks($raw);
            }
        }

        // ── 5. Custom Code ───────────────────────────────────────────────
        if ($request->has('customCode')) {
            $code = $request->input('customCode');
            if (is_array($code)) {
                $existing = is_array($footer->customCode) ? $footer->customCode : FooterConfig::defaultCustomCode();
                $footer->customCode = [
                    'enabled' => isset($code['enabled']) ? (bool) filter_var($code['enabled'], FILTER_VALIDATE_BOOLEAN) : (bool) ($existing['enabled'] ?? false),
                    'html'    => (string) ($code['html'] ?? $existing['html'] ?? ''),
                    'css'     => (string) ($code['css']  ?? $existing['css']  ?? ''),
                    'js'      => (string) ($code['js']   ?? $existing['js']   ?? ''),
                ];
                Log::info('Footer custom code updated', [
                    'user' => $request->user()?->id ?? 'admin',
                    'ip'   => $request->ip(),
                ]);
            }
        }

        $footer->save();

        return response()->json([
            'success' => true,
            'message' => 'Footer configuration updated successfully.',
            'data'    => $footer->toAdminPayload(),
        ], 200);
    }

    // ─────────────────────────────────────────────────────────────────────
    // GET /api/admin/footer/links
    // ─────────────────────────────────────────────────────────────────────
    public function getLinks(): JsonResponse
    {
        $footer = FooterConfig::getSingleton();
        $links  = $footer->getItemsList();

        return response()->json([
            'success' => true,
            'total'   => count($links),
            'links'   => $links,
            'items'   => $links,
            'data'    => $links,
        ], 200);
    }

    // ─────────────────────────────────────────────────────────────────────
    // PUT/POST /api/admin/footer/links — Auth Required
    // Replace footer links (supports nested submenu).
    // ─────────────────────────────────────────────────────────────────────
    public function updateLinks(Request $request): JsonResponse
    {
        $footer = FooterConfig::getSingleton();
        $raw    = $request->input('links', $request->input('items'));

        // Allow plain array in the body too
        if (is_null($raw)) {
            $all = $request->all();
            if (array_is_list($all)) {
                $raw = $all;
            }
        }

        if (! is_array($raw)) {
            return response()->json([
                'success' => false,
                'message' => 'Provide links as a JSON array under the "links" key.',
            ], 422);
        }

        // Unwrap nested { "links": [...] }
        if (isset($raw['links']) && is_array($raw['links'])) {
            $raw = $raw['links'];
        } elseif (isset($raw['items']) && is_array($raw['items'])) {
            $raw = $raw['items'];
        }

        $processed     = FooterConfig::normalizeLinks($raw);
        $footer->links = $processed;
        $footer->save();

        return response()->json([
            'success' => true,
            'message' => 'Footer links updated successfully.',
            'total'   => count($processed),
            'links'   => $processed,
            'items'   => $processed,
            'data'    => $footer->toAdminPayload(),
        ], 200);
    }

    // ─────────────────────────────────────────────────────────────────────
    // GET /api/admin/footer/cta
    // ─────────────────────────────────────────────────────────────────────
    public function getCta(): JsonResponse
    {
        $footer = FooterConfig::getSingleton();
        $cta    = is_array($footer->cta)
            ? array_merge(FooterConfig::defaultCta(), $footer->cta)
            : FooterConfig::defaultCta();

        return response()->json([
            'success' => true,
            'cta'     => $cta,
            'data'    => $cta,
        ], 200);
    }

    // ─────────────────────────────────────────────────────────────────────
    // PUT/POST /api/admin/footer/cta — Auth Required
    // ─────────────────────────────────────────────────────────────────────
    public function updateCta(Request $request): JsonResponse
    {
        $footer   = FooterConfig::getSingleton();
        $existing = is_array($footer->cta) ? array_merge(FooterConfig::defaultCta(), $footer->cta) : FooterConfig::defaultCta();

        $footer->cta = [
            'enabled'  => $request->has('enabled')  ? (bool) filter_var($request->input('enabled'),  FILTER_VALIDATE_BOOLEAN) : (bool) ($existing['enabled']  ?? true),
            'title'    => (string) $request->input('title',    $existing['title']    ?? ''),
            'subtitle' => (string) $request->input('subtitle', $existing['subtitle'] ?? ''),
            'label'    => (string) $request->input('label',    $existing['label']    ?? 'Schedule Consultation'),
            'url'      => (string) $request->input('url',      $existing['url']      ?? '/contact'),
        ];
        $footer->save();

        return response()->json([
            'success' => true,
            'message' => 'Footer CTA updated successfully.',
            'cta'     => $footer->cta,
            'data'    => $footer->cta,
        ], 200);
    }

    // ─────────────────────────────────────────────────────────────────────
    // GET /api/admin/footer/grid
    // ─────────────────────────────────────────────────────────────────────
    public function getGrid(): JsonResponse
    {
        $footer   = FooterConfig::getSingleton();
        $grid     = is_array($footer->grid) ? $footer->grid : FooterConfig::defaultGrid();
        $sections = is_array($footer->sections) && ! empty($footer->sections)
            ? $footer->sections
            : FooterConfig::defaultSections();

        $payload = array_merge($grid, ['sections' => array_values($sections)]);

        return response()->json([
            'success' => true,
            'grid'    => $payload,
            'data'    => $payload,
        ], 200);
    }

    // ─────────────────────────────────────────────────────────────────────
    // PUT/POST /api/admin/footer/grid — Auth Required
    // Saves columns, rows, and sections with full validation.
    // ─────────────────────────────────────────────────────────────────────
    public function updateGrid(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'columns'    => ['required', 'integer', 'between:1,4'],
            'rows'       => ['required', 'integer', 'between:1,3'],
            'sections'   => ['sometimes', 'array'],
            'sections.*' => ['nullable'],   // validated by FooterConfig::validateSections
        ], [
            'columns.between' => 'Columns must be between 1 and 4.',
            'rows.between'    => 'Rows must be between 1 and 3.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $cols = (int) $request->input('columns');
        $rows = (int) $request->input('rows');

        $footer       = FooterConfig::getSingleton();
        $footer->grid = ['columns' => $cols, 'rows' => $rows];

        if ($request->has('sections')) {
            $sections   = $request->input('sections');
            $validation = FooterConfig::validateSections($sections, $cols, $rows);
            if (! $validation['ok']) {
                return response()->json([
                    'success' => false,
                    'message' => $validation['error'],
                    'errors'  => ['sections' => [$validation['error']]],
                ], 422);
            }
            $footer->sections = array_values($sections);
        } else {
            // Auto-generate default sections for the new grid size
            $total    = $cols * $rows;
            $defaults = array_slice(
                array_merge(['about', 'quickLinks', 'services', 'contact'], array_fill(0, $total, '')),
                0, $total
            );
            $footer->sections = $defaults;
        }

        $footer->save();

        $payload = array_merge($footer->grid, ['sections' => array_values($footer->sections)]);

        return response()->json([
            'success' => true,
            'message' => 'Footer grid updated successfully.',
            'grid'    => $payload,
            'data'    => $payload,
        ], 200);
    }

    // ─────────────────────────────────────────────────────────────────────
    // GET /api/admin/footer/custom-code
    // ─────────────────────────────────────────────────────────────────────
    public function getCustomCode(): JsonResponse
    {
        $footer = FooterConfig::getSingleton();
        $code   = is_array($footer->customCode) ? $footer->customCode : FooterConfig::defaultCustomCode();

        return response()->json([
            'success'    => true,
            'customCode' => $code,
            'data'       => $code,
        ], 200);
    }

    // ─────────────────────────────────────────────────────────────────────
    // PUT/POST /api/admin/footer/custom-code — Auth Required
    // Security note: JS is persisted but should be rendered only
    // after Content-Security-Policy review in production.
    // All writes are logged with IP + user.
    // ─────────────────────────────────────────────────────────────────────
    public function updateCustomCode(Request $request): JsonResponse
    {
        $footer   = FooterConfig::getSingleton();
        $existing = is_array($footer->customCode) ? $footer->customCode : FooterConfig::defaultCustomCode();

        $footer->customCode = [
            'enabled' => $request->has('enabled') ? (bool) filter_var($request->input('enabled'), FILTER_VALIDATE_BOOLEAN) : (bool) ($existing['enabled'] ?? false),
            'html'    => (string) $request->input('html', $existing['html'] ?? ''),
            'css'     => (string) $request->input('css',  $existing['css']  ?? ''),
            'js'      => (string) $request->input('js',   $existing['js']   ?? ''),
        ];
        $footer->save();

        Log::warning('Footer custom code modified', [
            'user' => $request->user()?->id ?? 'admin',
            'ip'   => $request->ip(),
        ]);

        return response()->json([
            'success'    => true,
            'message'    => 'Footer custom code saved. Note: custom JavaScript is persisted and will be rendered in the browser — ensure it is reviewed before enabling in production.',
            'customCode' => $footer->customCode,
            'data'       => $footer->customCode,
        ], 200);
    }

    // ─────────────────────────────────────────────────────────────────────
    // POST /api/admin/footer/reset — Auth Required
    // ─────────────────────────────────────────────────────────────────────
    public function reset(): JsonResponse
    {
        $footer   = FooterConfig::getSingleton();
        $defaults = FooterConfig::defaults();

        $footer->layoutId   = $defaults['layoutId'];
        $footer->cta        = $defaults['cta'];
        $footer->grid       = $defaults['grid'];
        $footer->sections   = $defaults['sections'];
        $footer->links      = $defaults['links'];
        $footer->customCode = $defaults['customCode'];
        $footer->save();

        Layout::updateOrCreate(
            ['key' => 'footerLayout'],
            ['templateId' => 'layout1', 'updatedAt' => now()]
        );

        return response()->json([
            'success' => true,
            'message' => 'Footer configuration reset to defaults.',
            'data'    => $footer->toAdminPayload(),
        ], 200);
    }
}
