<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\HomeSection;
use App\Models\Layout;
use App\Models\NavbarConfig;
use App\Models\PageSeo;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminModuleController extends Controller
{
    /**
     * Module 1: Top Bar
     * GET /api/admin/modules/topbar
     */
    public function getTopbar(): JsonResponse
    {
        $layoutRecord = Layout::find('topbarLayout');
        $setting = SiteSetting::getSingleton();

        return response()->json([
            'success' => true,
            'module'  => 'topbar',
            'data'    => [
                'layout' => [
                    'key'        => 'topbarLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed'    => Layout::allowedFor('topbarLayout'),
                ],
                'content' => [
                    'email'        => $setting->email,
                    'phone'        => $setting->phone,
                    'socials'      => $setting->socials ?? [],
                    'announcement' => $setting->announcement ?? [],
                ],
            ],
        ], 200);
    }

    /**
     * Module 2: Navbar & Header Navigation
     * GET /api/admin/modules/navbar & /api/admin/modules/navigation/navbar
     */
    public function getNavbar(): JsonResponse
    {
        $layoutRecord = Layout::find('navbarLayout');
        $navbar = NavbarConfig::getSingleton();
        $adminPayload = $navbar->toAdminPayload();
        $currentTemplate = $layoutRecord?->templateId ?? 'layout1';
        $activeBehavior = $navbar->getBehaviorForLayout($currentTemplate);
        $isEnabled = $navbar->isNavbarEnabled();

        return response()->json([
            'success' => true,
            'module'  => 'navbar',
            'data'    => [
                'layout' => [
                    'key'        => 'navbarLayout',
                    'templateId' => $currentTemplate,
                    'layout'     => $currentTemplate,
                    'behavior'   => $activeBehavior,
                    'enabled'    => $isEnabled,
                    'visible'    => $isEnabled,
                    'status'     => $isEnabled ? 'visible' : 'hidden',
                    'allowed'    => Layout::allowedFor('navbarLayout'),
                ],
                'navigation' => $adminPayload,
            ],
        ], 200);
    }

    /**
     * Module 3: Home & Hero Sections
     * GET /api/admin/modules/home & /api/admin/modules/home-sections
     */
    public function getHome(): JsonResponse
    {
        $layoutRecord = Layout::find('homePageLayout');
        $home = HomeSection::getSingleton();
        $sections = $home->getMap();
        $activeCount = count(array_filter($sections));

        return response()->json([
            'success'     => true,
            'module'      => 'home',
            'activeCount' => $activeCount,
            'totalCount'  => count($sections),
            'items'       => $home->getDetailedList(),
            'sections'    => $sections,
            'data'        => [
                'layout' => [
                    'key'        => 'homePageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed'    => Layout::allowedFor('homePageLayout'),
                ],
                'activeCount' => $activeCount,
                'totalCount'  => count($sections),
                'items'       => $home->getDetailedList(),
                'sections'    => $sections,
            ],
        ], 200);
    }

    /**
     * Module 4: Services Catalog
     * GET /api/admin/modules/services & /api/admin/modules/api/public/services
     */
    public function getServices(Request $request): JsonResponse
    {
        $layoutRecord = Layout::find('servicesPageLayout');
        $services = Service::orderBy('order')->orderBy('id')->get()->map(fn ($s) => $s->toAdminPayload());

        return response()->json([
            'success' => true,
            'module'  => 'services',
            'data'    => [
                'layout' => [
                    'key'        => 'servicesPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed'    => Layout::allowedFor('servicesPageLayout'),
                ],
                'total'    => $services->count(),
                'services' => $services,
            ],
        ], 200);
    }

    /**
     * Module 4.1: Service Detail
     * GET /api/admin/modules/service-detail
     */
    public function getServiceDetail(): JsonResponse
    {
        $layoutRecord = Layout::find('serviceDetailPageLayout');
        $defaultSeo = PageSeo::find('service-detail')?->toPayload();

        return response()->json([
            'success' => true,
            'module'  => 'service-detail',
            'data'    => [
                'layout' => [
                    'key'        => 'serviceDetailPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed'    => Layout::allowedFor('serviceDetailPageLayout'),
                ],
                'defaultSeo' => $defaultSeo,
            ],
        ], 200);
    }

    /**
     * Module 5: Portfolio Catalog
     * GET /api/admin/modules/portfolio
     */
    public function getPortfolio(Request $request): JsonResponse
    {
        $layoutRecord = Layout::find('portfolioPageLayout');
        $portfolio = Portfolio::orderBy('order')->orderBy('id')->get()->map(fn ($p) => $p->toAdminPayload());

        return response()->json([
            'success' => true,
            'module'  => 'portfolio',
            'data'    => [
                'layout' => [
                    'key'        => 'portfolioPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed'    => Layout::allowedFor('portfolioPageLayout'),
                ],
                'total'     => $portfolio->count(),
                'portfolio' => $portfolio,
            ],
        ], 200);
    }

    /**
     * Module 5.1: Portfolio Detail (Case Study)
     * GET /api/admin/modules/portfolio-detail
     */
    public function getPortfolioDetail(): JsonResponse
    {
        $layoutRecord = Layout::find('portfolioDetailPageLayout');
        $defaultSeo = PageSeo::find('portfolio-detail')?->toPayload();

        return response()->json([
            'success' => true,
            'module'  => 'portfolio-detail',
            'data'    => [
                'layout' => [
                    'key'        => 'portfolioDetailPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed'    => Layout::allowedFor('portfolioDetailPageLayout'),
                ],
                'defaultSeo' => $defaultSeo,
            ],
        ], 200);
    }

    /**
     * Module 6: Blog & Magazine Feed
     * GET /api/admin/modules/blog
     */
    public function getBlog(Request $request): JsonResponse
    {
        $layoutRecord = Layout::find('blogPageLayout');
        $blogs = Blog::orderBy('date', 'desc')->orderBy('id', 'desc')->get()->map(fn ($b) => $b->toAdminPayload());

        return response()->json([
            'success' => true,
            'module'  => 'blog',
            'data'    => [
                'layout' => [
                    'key'        => 'blogPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed'    => Layout::allowedFor('blogPageLayout'),
                ],
                'total' => $blogs->count(),
                'posts' => $blogs,
            ],
        ], 200);
    }

    /**
     * Module 6.1: Blog Article Detail
     * GET /api/admin/modules/blog-detail
     */
    public function getBlogDetail(): JsonResponse
    {
        $layoutRecord = Layout::find('blogDetailPageLayout');
        $defaultSeo = PageSeo::find('blog-detail')?->toPayload();

        return response()->json([
            'success' => true,
            'module'  => 'blog-detail',
            'data'    => [
                'layout' => [
                    'key'        => 'blogDetailPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed'    => Layout::allowedFor('blogDetailPageLayout'),
                ],
                'defaultSeo' => $defaultSeo,
            ],
        ], 200);
    }

    /**
     * Module Settings: Site Identity & Config
     * GET /api/admin/modules/settings
     */
    public function getSettings(): JsonResponse
    {
        $setting = SiteSetting::getSingleton();

        return response()->json([
            'success' => true,
            'module'  => 'settings',
            'data'    => $setting->toPayload(),
        ], 200);
    }

    /**
     * Module Layouts: All Layouts
     * GET /api/admin/modules/layouts
     */
    public function getLayouts(): JsonResponse
    {
        $records = Layout::all()->keyBy('key');
        $modules = [];
        $active  = [];

        $navbar = NavbarConfig::getSingleton();
        $currNavLayout = $records->get('navbarLayout')?->templateId ?? 'layout1';
        $navBehavior = $navbar->getBehaviorForLayout($currNavLayout);

        foreach (Layout::$modules as $key => $meta) {
            $current = $records->get($key)?->templateId ?? 'layout1';

            if ($key === 'navbarLayout') {
                $isEnabled = $navbar->isNavbarEnabled();
                $active[$key] = [
                    'layout'   => $current,
                    'behavior' => $navBehavior,
                    'enabled'  => $isEnabled,
                    'visible'  => $isEnabled,
                    'status'   => $isEnabled ? 'visible' : 'hidden',
                ];
            } else {
                $active[$key] = $current;
            }

            $modules[] = [
                'key'        => $key,
                'name'       => $meta['name'],
                'templateId' => $current,
                'allowed'    => Layout::allowedFor($key),
                'updatedAt'  => $records->get($key)?->updatedAt,
            ];
        }

        return response()->json([
            'success' => true,
            'module'  => 'layouts',
            'data'    => [
                'active'  => $active,
                'modules' => $modules,
            ],
        ], 200);
    }
}
