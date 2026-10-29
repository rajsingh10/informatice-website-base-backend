<?php

namespace App\Http\Controllers;

use App\Models\Layout;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TopbarController extends Controller
{
    /**
     * Default social platforms configuration.
     */
    public static array $defaultSocialPlatforms = [
        'facebook'  => ['label' => 'Facebook',  'icon' => 'fa-brands fa-facebook-f'],
        'twitter'   => ['label' => 'Twitter',   'icon' => 'fa-brands fa-twitter'],
        'instagram' => ['label' => 'Instagram', 'icon' => 'fa-brands fa-instagram'],
        'linkedin'  => ['label' => 'LinkedIn',  'icon' => 'fa-brands fa-linkedin-in'],
        'youtube'   => ['label' => 'YouTube',   'icon' => 'fa-brands fa-youtube'],
        'github'    => ['label' => 'GitHub',    'icon' => 'fa-brands fa-github'],
        'whatsapp'  => ['label' => 'WhatsApp',  'icon' => 'fa-brands fa-whatsapp'],
    ];

    /**
     * Determine if topbar is enabled.
     */
    public static function isTopbarEnabled(): bool
    {
        $layoutRecord = Layout::find('topbarLayout');
        $templateId = $layoutRecord?->templateId ?? 'layout1';

        if ($templateId === 'layout0') {
            return false;
        }

        $setting = SiteSetting::getSingleton();
        $announcement = is_array($setting->announcement) ? $setting->announcement : [];

        if (isset($announcement['_topbar']['enabled'])) {
            return (bool) $announcement['_topbar']['enabled'];
        }

        return true;
    }

    /**
     * Get active layout template ID for topbar.
     */
    public static function getActiveTemplate(): string
    {
        $layoutRecord = Layout::find('topbarLayout');
        $template = $layoutRecord?->templateId ?? 'layout1';
        return $template === 'layout0' ? 'layout1' : $template;
    }

    /**
     * Extract structured social links list.
     */
    public static function getSocialLinksList(SiteSetting $setting): array
    {
        $announcement = is_array($setting->announcement) ? $setting->announcement : [];
        if (! empty($announcement['_topbar_social_links']) && is_array($announcement['_topbar_social_links'])) {
            return array_values($announcement['_topbar_social_links']);
        }

        $rawSocials = is_array($setting->socials) ? $setting->socials : [];
        $list = [];
        $idx = 1;

        foreach ($rawSocials as $key => $val) {
            if (is_array($val)) {
                $platform = strtolower($val['platform'] ?? $val['name'] ?? $key);
                $meta = static::$defaultSocialPlatforms[$platform] ?? ['label' => ucfirst($platform), 'icon' => 'fa-solid fa-link'];
                $list[] = [
                    'id'       => (string) ($val['id'] ?? "soc-{$idx}"),
                    'platform' => $platform,
                    'label'    => (string) ($val['label'] ?? $meta['label']),
                    'icon'     => (string) ($val['icon'] ?? $meta['icon']),
                    'url'      => (string) ($val['url'] ?? ''),
                    'enabled'  => isset($val['enabled']) ? filter_var($val['enabled'], FILTER_VALIDATE_BOOLEAN) : true,
                ];
                $idx++;
            } elseif (is_string($val)) {
                $platform = strtolower($key);
                $meta = static::$defaultSocialPlatforms[$platform] ?? ['label' => ucfirst($platform), 'icon' => 'fa-solid fa-link'];
                $list[] = [
                    'id'       => "soc-{$idx}",
                    'platform' => $platform,
                    'label'    => $meta['label'],
                    'icon'     => $meta['icon'],
                    'url'      => (string) $val,
                    'enabled'  => ! empty($val),
                ];
                $idx++;
            }
        }

        if (empty($list)) {
            $list = [
                ['id' => 'soc-1', 'platform' => 'facebook',  'label' => 'Facebook',  'icon' => 'fa-brands fa-facebook-f',  'url' => 'https://facebook.com/nexgen',          'enabled' => true],
                ['id' => 'soc-2', 'platform' => 'twitter',   'label' => 'Twitter',   'icon' => 'fa-brands fa-twitter',     'url' => 'https://twitter.com/nexgen',           'enabled' => true],
                ['id' => 'soc-3', 'platform' => 'instagram', 'label' => 'Instagram', 'icon' => 'fa-brands fa-instagram',   'url' => 'https://instagram.com/nexgen',         'enabled' => true],
                ['id' => 'soc-4', 'platform' => 'linkedin',  'label' => 'LinkedIn',  'icon' => 'fa-brands fa-linkedin-in', 'url' => 'https://linkedin.com/company/nexgen', 'enabled' => true],
            ];
        }

        return $list;
    }

    /**
     * Get announcement / notice object.
     */
    public static function getAnnouncementData(SiteSetting $setting): array
    {
        $raw = is_array($setting->announcement) ? $setting->announcement : [];

        return [
            'enabled' => isset($raw['enabled']) ? filter_var($raw['enabled'], FILTER_VALIDATE_BOOLEAN) : true,
            'badge'   => (string) ($raw['badge'] ?? 'Notice'),
            'text'    => (string) ($raw['text'] ?? 'Transforming Ideas Into Digital Reality'),
            'url'     => (string) ($raw['url'] ?? ''),
        ];
    }

    /**
     * Get CTA button data.
     */
    public static function getCtaData(SiteSetting $setting): array
    {
        $announcement = is_array($setting->announcement) ? $setting->announcement : [];
        $rawCta = $announcement['cta'] ?? ($announcement['_topbar']['cta'] ?? null);

        if (is_array($rawCta)) {
            return [
                'enabled' => isset($rawCta['enabled']) ? filter_var($rawCta['enabled'], FILTER_VALIDATE_BOOLEAN) : true,
                'label'   => (string) ($rawCta['label'] ?? 'Get a Quote'),
                'url'     => (string) ($rawCta['url'] ?? '/contact'),
            ];
        }

        return [
            'enabled' => true,
            'label'   => 'Get a Quote',
            'url'     => '/contact',
        ];
    }

    /**
     * Build standard payload.
     */
    public static function buildPayload(bool $forAdmin = false): array
    {
        $setting = SiteSetting::getSingleton();
        $layoutRecord = Layout::find('topbarLayout');
        $rawTemplate = $layoutRecord?->templateId ?? 'layout1';
        $isEnabled = static::isTopbarEnabled();
        $activeTemplate = $rawTemplate === 'layout0' ? 'layout1' : $rawTemplate;

        $email = (string) ($setting->email ?: 'hello@nexgensolutions.com');
        $phone = (string) ($setting->phone ?: '+91 98765 43210');
        $announcement = static::getAnnouncementData($setting);
        $cta = static::getCtaData($setting);
        $socialLinks = static::getSocialLinksList($setting);

        // Map format for legacy frontend: { facebook: url, twitter: url, ... }
        $socialsMap = [];
        foreach ($socialLinks as $item) {
            if ($forAdmin || ! empty($item['enabled'])) {
                $socialsMap[$item['platform']] = $item['url'];
            }
        }

        $filteredSocialLinks = $forAdmin ? $socialLinks : array_values(array_filter($socialLinks, fn ($s) => ! empty($s['enabled'])));

        $payload = [
            'layout'       => $isEnabled ? $activeTemplate : 'layout0',
            'templateId'   => $activeTemplate,
            'enabled'      => $isEnabled,
            'visible'      => $isEnabled,
            'status'       => $isEnabled ? 'visible' : 'hidden',
            'email'        => $email,
            'phone'        => $phone,
            'announcement' => $announcement,
            'notice'       => $announcement,
            'cta'          => $cta,
            'button'       => $cta,
            'socials'      => $socialsMap,
            'socialLinks'  => $filteredSocialLinks,
            'items'        => $filteredSocialLinks,
        ];

        if ($forAdmin) {
            $payload['allowed'] = Layout::allowedFor('topbarLayout');
            $payload['allowedTemplates'] = Layout::allowedFor('topbarLayout');
        }

        return $payload;
    }

    // ────────────────────────────────────────────────────────────────────────
    // 1. VIEW APIs (Public & Admin)
    // ────────────────────────────────────────────────────────────────────────

    /**
     * GET /api/public/topbar & /public/topbar
     * Public topbar payload (only enabled items).
     */
    public function showPublic(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => static::buildPayload(false),
        ], 200);
    }

    /**
     * GET /api/admin/topbar & /admin/topbar & /api/admin/modules/topbar
     * Admin topbar configuration (includes disabled items and allowed layouts).
     */
    public function showAdmin(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'module'  => 'topbar',
            'data'    => static::buildPayload(true),
        ], 200);
    }

    // ────────────────────────────────────────────────────────────────────────
    // 2. CREATE / UPDATE APIs
    // ────────────────────────────────────────────────────────────────────────

    /**
     * POST/PUT /api/admin/topbar & /admin/topbar & /api/public/topbar
     * Create or full/partial update of topbar configuration.
     */
    public function saveTopbar(Request $request): JsonResponse
    {
        $setting = SiteSetting::getSingleton();
        $layoutRecord = Layout::find('topbarLayout');
        $currentTemplate = $layoutRecord?->templateId ?? 'layout1';

        // 1. Template / Layout Update
        $newTemplate = $request->input('templateId', $request->input('layout'));
        if ($newTemplate !== null && in_array($newTemplate, Layout::allowedFor('topbarLayout'))) {
            Layout::updateOrCreate(
                ['key' => 'topbarLayout'],
                ['templateId' => $newTemplate, 'updatedAt' => now()]
            );
            $currentTemplate = $newTemplate;
        }

        // 2. Enabled / Visibility Status
        $announcement = is_array($setting->announcement) ? $setting->announcement : [];
        if (! isset($announcement['_topbar'])) {
            $announcement['_topbar'] = [];
        }

        if ($request->has('enabled') || $request->has('visible') || $request->has('status')) {
            $isEnabled = true;
            if ($request->has('enabled')) {
                $isEnabled = filter_var($request->input('enabled'), FILTER_VALIDATE_BOOLEAN);
            } elseif ($request->has('visible')) {
                $isEnabled = filter_var($request->input('visible'), FILTER_VALIDATE_BOOLEAN);
            } elseif ($request->has('status')) {
                $st = (string) $request->input('status');
                $isEnabled = in_array(strtolower($st), ['visible', 'on', 'true', '1'], true);
            }

            $announcement['_topbar']['enabled'] = $isEnabled;

            // If layout wasn't explicitly changed, sync layout0 vs active template
            if ($newTemplate === null) {
                if (! $isEnabled && $currentTemplate !== 'layout0') {
                    $announcement['_topbar']['last_template'] = $currentTemplate;
                    Layout::updateOrCreate(['key' => 'topbarLayout'], ['templateId' => 'layout0', 'updatedAt' => now()]);
                } elseif ($isEnabled && $currentTemplate === 'layout0') {
                    $restore = $announcement['_topbar']['last_template'] ?? 'layout1';
                    Layout::updateOrCreate(['key' => 'topbarLayout'], ['templateId' => $restore, 'updatedAt' => now()]);
                }
            }
        }

        // 3. Contact Info (Email / Phone)
        if ($request->has('email')) {
            $setting->email = (string) $request->input('email');
        }
        if ($request->has('phone')) {
            $setting->phone = (string) $request->input('phone');
        }

        // 4. Announcement / Notice
        if ($request->has('announcement') || $request->has('notice')) {
            $noticeInput = $request->input('announcement', $request->input('notice'));
            if (is_array($noticeInput)) {
                if (isset($noticeInput['enabled'])) {
                    $announcement['enabled'] = filter_var($noticeInput['enabled'], FILTER_VALIDATE_BOOLEAN);
                }
                if (isset($noticeInput['badge'])) {
                    $announcement['badge'] = (string) $noticeInput['badge'];
                }
                if (isset($noticeInput['text'])) {
                    $announcement['text'] = (string) $noticeInput['text'];
                }
                if (isset($noticeInput['url'])) {
                    $announcement['url'] = (string) $noticeInput['url'];
                }
            } elseif (is_string($noticeInput)) {
                $announcement['text'] = $noticeInput;
                $announcement['enabled'] = true;
            }
        }

        // 5. CTA Button
        if ($request->has('cta') || $request->has('button')) {
            $ctaInput = $request->input('cta', $request->input('button'));
            if (is_array($ctaInput)) {
                $existingCta = static::getCtaData($setting);
                $announcement['cta'] = [
                    'enabled' => isset($ctaInput['enabled']) ? filter_var($ctaInput['enabled'], FILTER_VALIDATE_BOOLEAN) : $existingCta['enabled'],
                    'label'   => (string) ($ctaInput['label'] ?? $existingCta['label']),
                    'url'     => (string) ($ctaInput['url'] ?? $existingCta['url']),
                ];
            }
        }

        // 6. Social Links / Items
        if ($request->has('socialLinks') || $request->has('items') || $request->has('socials')) {
            $socialInput = $request->input('socialLinks', $request->input('items', $request->input('socials')));
            if (is_array($socialInput)) {
                if (array_is_list($socialInput)) {
                    // Indexed list of items [{ id, platform, label, icon, url, enabled }]
                    $processed = [];
                    foreach ($socialInput as $idx => $item) {
                        if (! is_array($item)) continue;
                        $platform = strtolower($item['platform'] ?? $item['name'] ?? 'link');
                        $meta = static::$defaultSocialPlatforms[$platform] ?? ['label' => ucfirst($platform), 'icon' => 'fa-solid fa-link'];
                        $processed[] = [
                            'id'       => (string) ($item['id'] ?? 'soc-' . ($idx + 1)),
                            'platform' => $platform,
                            'label'    => (string) ($item['label'] ?? $meta['label']),
                            'icon'     => (string) ($item['icon'] ?? $meta['icon']),
                            'url'      => (string) ($item['url'] ?? ''),
                            'enabled'  => isset($item['enabled']) ? filter_var($item['enabled'], FILTER_VALIDATE_BOOLEAN) : true,
                        ];
                    }
                    $announcement['_topbar_social_links'] = $processed;

                    // Also sync key-value in setting->socials
                    $newMap = [];
                    foreach ($processed as $p) {
                        $newMap[$p['platform']] = $p['url'];
                    }
                    $setting->socials = $newMap;
                } else {
                    // Associative map { "facebook": "https://..." }
                    $currentList = static::getSocialLinksList($setting);
                    $listByPlatform = [];
                    foreach ($currentList as $item) {
                        $listByPlatform[$item['platform']] = $item;
                    }

                    $idx = count($listByPlatform) + 1;
                    foreach ($socialInput as $plat => $url) {
                        $plat = strtolower($plat);
                        $meta = static::$defaultSocialPlatforms[$plat] ?? ['label' => ucfirst($plat), 'icon' => 'fa-solid fa-link'];
                        if (isset($listByPlatform[$plat])) {
                            $listByPlatform[$plat]['url'] = (string) $url;
                            $listByPlatform[$plat]['enabled'] = ! empty($url);
                        } else {
                            $listByPlatform[$plat] = [
                                'id'       => "soc-{$idx}",
                                'platform' => $plat,
                                'label'    => $meta['label'],
                                'icon'     => $meta['icon'],
                                'url'      => (string) $url,
                                'enabled'  => ! empty($url),
                            ];
                            $idx++;
                        }
                    }
                    $announcement['_topbar_social_links'] = array_values($listByPlatform);
                    $setting->socials = $socialInput;
                }
            }
        }

        $setting->announcement = $announcement;
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => 'Topbar configuration updated successfully.',
            'data'    => static::buildPayload(true),
        ], 200);
    }

    // ────────────────────────────────────────────────────────────────────────
    // 3. ENABLE / DISABLE (TOGGLE)
    // ────────────────────────────────────────────────────────────────────────

    /**
     * POST/PUT /api/admin/topbar/toggle & /admin/topbar/toggle & /api/public/topbar/toggle
     * Toggle overall topbar visibility.
     */
    public function toggle(Request $request): JsonResponse
    {
        $setting = SiteSetting::getSingleton();
        $announcement = is_array($setting->announcement) ? $setting->announcement : [];
        if (! isset($announcement['_topbar'])) {
            $announcement['_topbar'] = [];
        }

        $currentlyEnabled = static::isTopbarEnabled();

        // If explicitly requested
        if ($request->has('enabled')) {
            $targetEnabled = filter_var($request->input('enabled'), FILTER_VALIDATE_BOOLEAN);
        } elseif ($request->has('status')) {
            $targetEnabled = in_array(strtolower((string) $request->input('status')), ['visible', 'on', 'true', '1'], true);
        } else {
            $targetEnabled = ! $currentlyEnabled;
        }

        $announcement['_topbar']['enabled'] = $targetEnabled;

        // Sync layout template
        $layoutRecord = Layout::find('topbarLayout');
        $currentTemplate = $layoutRecord?->templateId ?? 'layout1';

        if (! $targetEnabled) {
            if ($currentTemplate !== 'layout0') {
                $announcement['_topbar']['last_template'] = $currentTemplate;
            }
            Layout::updateOrCreate(['key' => 'topbarLayout'], ['templateId' => 'layout0', 'updatedAt' => now()]);
        } else {
            $restore = $announcement['_topbar']['last_template'] ?? ($currentTemplate === 'layout0' ? 'layout1' : $currentTemplate);
            Layout::updateOrCreate(['key' => 'topbarLayout'], ['templateId' => $restore, 'updatedAt' => now()]);
        }

        $setting->announcement = $announcement;
        $setting->save();

        $action = $targetEnabled ? 'enabled (visible)' : 'disabled (hidden)';
        return response()->json([
            'success' => true,
            'message' => "Topbar is now {$action}.",
            'data'    => static::buildPayload(true),
        ], 200);
    }

    // ────────────────────────────────────────────────────────────────────────
    // 4. DELETE / RESET API
    // ────────────────────────────────────────────────────────────────────────

    /**
     * DELETE /api/admin/topbar & /admin/topbar
     * Disables or resets topbar to hidden/empty state.
     */
    public function destroy(): JsonResponse
    {
        $setting = SiteSetting::getSingleton();
        $announcement = is_array($setting->announcement) ? $setting->announcement : [];
        $announcement['_topbar']['enabled'] = false;
        $setting->announcement = $announcement;
        $setting->save();

        Layout::updateOrCreate(['key' => 'topbarLayout'], ['templateId' => 'layout0', 'updatedAt' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Topbar has been disabled and set to hidden (layout0).',
            'data'    => static::buildPayload(true),
        ], 200);
    }

    // ────────────────────────────────────────────────────────────────────────
    // 5. TOPBAR ITEMS / SOCIALS CRUD (Add, Edit, Toggle, Delete)
    // ────────────────────────────────────────────────────────────────────────

    /**
     * GET /api/admin/topbar/items & /admin/topbar/items
     * Returns topbar social links and items.
     */
    public function getItems(): JsonResponse
    {
        $setting = SiteSetting::getSingleton();
        $items = static::getSocialLinksList($setting);

        return response()->json([
            'success' => true,
            'total'   => count($items),
            'items'   => $items,
            'data'    => $items,
        ], 200);
    }

    /**
     * POST /api/admin/topbar/items & /admin/topbar/items & /admin/topbar/socials
     * Add a new link/social to topbar.
     */
    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'url' => ['required', 'string'],
        ]);

        $setting = SiteSetting::getSingleton();
        $items = static::getSocialLinksList($setting);

        // Determine next ID
        $maxNum = 0;
        foreach ($items as $it) {
            if (isset($it['id']) && preg_match('/(?:soc|top)-(\d+)/', (string) $it['id'], $m)) {
                $num = (int) $m[1];
                if ($num > $maxNum) $maxNum = $num;
            }
        }
        $newId = 'soc-' . ($maxNum + 1);

        $platform = strtolower($request->input('platform', $request->input('name', 'custom')));
        $meta = static::$defaultSocialPlatforms[$platform] ?? ['label' => ucfirst($platform), 'icon' => 'fa-solid fa-link'];

        $newItem = [
            'id'       => $newId,
            'platform' => $platform,
            'label'    => (string) ($request->input('label', $request->input('title', $meta['label']))),
            'icon'     => (string) ($request->input('icon', $meta['icon'])),
            'url'      => (string) $request->input('url'),
            'enabled'  => $request->has('enabled') ? filter_var($request->input('enabled'), FILTER_VALIDATE_BOOLEAN) : true,
        ];

        $items[] = $newItem;

        // Persist
        $announcement = is_array($setting->announcement) ? $setting->announcement : [];
        $announcement['_topbar_social_links'] = $items;
        $setting->announcement = $announcement;

        // Update socials map
        $map = is_array($setting->socials) ? $setting->socials : [];
        $map[$platform] = $newItem['url'];
        $setting->socials = $map;

        $setting->save();

        return response()->json([
            'success' => true,
            'message' => "Topbar link '{$newItem['label']}' added successfully.",
            'item'    => $newItem,
            'items'   => $items,
        ], 201);
    }

    /**
     * PUT/POST /api/admin/topbar/items/{id} & /admin/topbar/items/{id}
     * Edit a specific topbar link by ID or platform name.
     */
    public function updateItem(Request $request, string $id): JsonResponse
    {
        $setting = SiteSetting::getSingleton();
        $items = static::getSocialLinksList($setting);
        $found = false;
        $updatedItem = null;

        foreach ($items as $idx => $it) {
            if ((string) ($it['id'] ?? '') === $id || strtolower($it['platform'] ?? '') === strtolower($id)) {
                if ($request->has('label') || $request->has('title')) {
                    $items[$idx]['label'] = (string) $request->input('label', $request->input('title'));
                }
                if ($request->has('url')) {
                    $items[$idx]['url'] = (string) $request->input('url');
                }
                if ($request->has('icon')) {
                    $items[$idx]['icon'] = (string) $request->input('icon');
                }
                if ($request->has('platform')) {
                    $items[$idx]['platform'] = strtolower((string) $request->input('platform'));
                }
                if ($request->has('enabled')) {
                    $items[$idx]['enabled'] = filter_var($request->input('enabled'), FILTER_VALIDATE_BOOLEAN);
                }

                $updatedItem = $items[$idx];
                $found = true;
                break;
            }
        }

        if (! $found) {
            return response()->json([
                'success' => false,
                'message' => "Topbar item '{$id}' not found.",
            ], 404);
        }

        $announcement = is_array($setting->announcement) ? $setting->announcement : [];
        $announcement['_topbar_social_links'] = $items;
        $setting->announcement = $announcement;

        $map = is_array($setting->socials) ? $setting->socials : [];
        if (! empty($updatedItem['platform'])) {
            $map[$updatedItem['platform']] = $updatedItem['url'];
        }
        $setting->socials = $map;
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => "Topbar link '{$updatedItem['label']}' updated successfully.",
            'item'    => $updatedItem,
            'items'   => $items,
        ], 200);
    }

    /**
     * POST/PUT /api/admin/topbar/items/{id}/toggle & /admin/topbar/items/{id}/toggle
     * Toggle enabled state of a specific topbar link.
     */
    public function toggleItem(Request $request, string $id): JsonResponse
    {
        $setting = SiteSetting::getSingleton();
        $items = static::getSocialLinksList($setting);
        $found = false;
        $updatedItem = null;

        foreach ($items as $idx => $it) {
            if ((string) ($it['id'] ?? '') === $id || strtolower($it['platform'] ?? '') === strtolower($id)) {
                $current = ! empty($it['enabled']);
                $newVal = $request->has('enabled')
                    ? filter_var($request->input('enabled'), FILTER_VALIDATE_BOOLEAN)
                    : ! $current;

                $items[$idx]['enabled'] = $newVal;
                $updatedItem = $items[$idx];
                $found = true;
                break;
            }
        }

        if (! $found) {
            return response()->json([
                'success' => false,
                'message' => "Topbar item '{$id}' not found.",
            ], 404);
        }

        $announcement = is_array($setting->announcement) ? $setting->announcement : [];
        $announcement['_topbar_social_links'] = $items;
        $setting->announcement = $announcement;
        $setting->save();

        $stateText = $updatedItem['enabled'] ? 'enabled' : 'disabled';
        return response()->json([
            'success' => true,
            'message' => "Topbar link '{$updatedItem['label']}' {$stateText}.",
            'item'    => $updatedItem,
            'items'   => $items,
        ], 200);
    }

    /**
     * DELETE /api/admin/topbar/items/{id} & /admin/topbar/items/{id}
     * Delete a link/social item from topbar.
     */
    public function deleteItem(string $id): JsonResponse
    {
        $setting = SiteSetting::getSingleton();
        $items = static::getSocialLinksList($setting);
        $filtered = [];
        $removed = null;

        foreach ($items as $it) {
            if ((string) ($it['id'] ?? '') === $id || strtolower($it['platform'] ?? '') === strtolower($id)) {
                $removed = $it;
            } else {
                $filtered[] = $it;
            }
        }

        if (! $removed) {
            return response()->json([
                'success' => false,
                'message' => "Topbar item '{$id}' not found.",
            ], 404);
        }

        $announcement = is_array($setting->announcement) ? $setting->announcement : [];
        $announcement['_topbar_social_links'] = array_values($filtered);
        $setting->announcement = $announcement;

        $map = is_array($setting->socials) ? $setting->socials : [];
        if (! empty($removed['platform'])) {
            unset($map[$removed['platform']]);
        }
        $setting->socials = $map;
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => "Topbar link '{$removed['label']}' deleted.",
            'deleted' => $removed,
            'items'   => array_values($filtered),
        ], 200);
    }

    // ────────────────────────────────────────────────────────────────────────
    // 6. SPECIFIC COMPONENT ENDPOINTS (Contact, Notice, CTA)
    // ────────────────────────────────────────────────────────────────────────

    /**
     * PUT/POST /api/admin/topbar/contact
     * Update email and phone only.
     */
    public function updateContact(Request $request): JsonResponse
    {
        $setting = SiteSetting::getSingleton();

        if ($request->has('email')) {
            $setting->email = (string) $request->input('email');
        }
        if ($request->has('phone')) {
            $setting->phone = (string) $request->input('phone');
        }
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => 'Topbar contact info updated.',
            'data'    => [
                'email' => $setting->email,
                'phone' => $setting->phone,
            ],
        ], 200);
    }

    /**
     * PUT/POST /api/admin/topbar/notice & /admin/topbar/announcement
     * Update notice/announcement only.
     */
    public function updateNotice(Request $request): JsonResponse
    {
        $setting = SiteSetting::getSingleton();
        $announcement = is_array($setting->announcement) ? $setting->announcement : [];

        if ($request->has('enabled')) {
            $announcement['enabled'] = filter_var($request->input('enabled'), FILTER_VALIDATE_BOOLEAN);
        }
        if ($request->has('badge')) {
            $announcement['badge'] = (string) $request->input('badge');
        }
        if ($request->has('text')) {
            $announcement['text'] = (string) $request->input('text');
        }
        if ($request->has('url')) {
            $announcement['url'] = (string) $request->input('url');
        }

        $setting->announcement = $announcement;
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => 'Topbar notice updated.',
            'data'    => static::getAnnouncementData($setting),
        ], 200);
    }

    /**
     * POST/PUT /api/admin/topbar/notice/toggle
     * Toggle announcement/notice on or off.
     */
    public function toggleNotice(): JsonResponse
    {
        $setting = SiteSetting::getSingleton();
        $announcement = is_array($setting->announcement) ? $setting->announcement : [];
        $current = isset($announcement['enabled']) ? (bool) $announcement['enabled'] : true;
        $announcement['enabled'] = ! $current;

        $setting->announcement = $announcement;
        $setting->save();

        $action = $announcement['enabled'] ? 'enabled' : 'disabled';
        return response()->json([
            'success' => true,
            'message' => "Topbar notice is now {$action}.",
            'data'    => static::getAnnouncementData($setting),
        ], 200);
    }

    /**
     * PUT/POST /api/admin/topbar/cta
     * Update CTA button label and URL.
     */
    public function updateCta(Request $request): JsonResponse
    {
        $setting = SiteSetting::getSingleton();
        $announcement = is_array($setting->announcement) ? $setting->announcement : [];
        $existing = static::getCtaData($setting);

        $announcement['cta'] = [
            'enabled' => $request->has('enabled') ? filter_var($request->input('enabled'), FILTER_VALIDATE_BOOLEAN) : $existing['enabled'],
            'label'   => (string) ($request->input('label', $existing['label'])),
            'url'     => (string) ($request->input('url', $existing['url'])),
        ];

        $setting->announcement = $announcement;
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => 'Topbar CTA button updated.',
            'data'    => $announcement['cta'],
        ], 200);
    }

    /**
     * POST/PUT /api/admin/topbar/cta/toggle
     * Toggle CTA button on or off.
     */
    public function toggleCta(): JsonResponse
    {
        $setting = SiteSetting::getSingleton();
        $announcement = is_array($setting->announcement) ? $setting->announcement : [];
        $existing = static::getCtaData($setting);
        $announcement['cta'] = [
            'enabled' => ! $existing['enabled'],
            'label'   => $existing['label'],
            'url'     => $existing['url'],
        ];

        $setting->announcement = $announcement;
        $setting->save();

        $action = $announcement['cta']['enabled'] ? 'enabled' : 'disabled';
        return response()->json([
            'success' => true,
            'message' => "Topbar CTA button is now {$action}.",
            'data'    => $announcement['cta'],
        ], 200);
    }
}

