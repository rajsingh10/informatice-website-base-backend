<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TopbarController;
use App\Models\Blog;
use App\Models\Career;
use App\Models\Event;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\HomeSection; // Contact Page API
use App\Models\Inquiry;
use App\Models\Layout;
use App\Models\LegalPage;
use App\Models\NavbarConfig;
use App\Models\PageSeo;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\Testimonial;
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
        $payload = TopbarController::buildPayload(true);
        $setting = SiteSetting::getSingleton();

        return response()->json([
            'success' => true,
            'module' => 'topbar',
            'data' => array_merge($payload, [
                'layout' => [
                    'key' => 'topbarLayout',
                    'templateId' => $payload['templateId'],
                    'layout' => $payload['layout'],
                    'enabled' => $payload['enabled'],
                    'visible' => $payload['visible'],
                    'status' => $payload['status'],
                    'allowed' => Layout::allowedFor('topbarLayout'),
                ],
                'content' => [
                    'email' => $payload['email'],
                    'phone' => $payload['phone'],
                    'socials' => $payload['socials'],
                    'announcement' => $payload['announcement'],
                    'cta' => $payload['cta'],
                ],
            ]),
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
            'module' => 'navbar',
            'data' => [
                'layout' => [
                    'key' => 'navbarLayout',
                    'templateId' => $currentTemplate,
                    'layout' => $currentTemplate,
                    'behavior' => $activeBehavior,
                    'enabled' => $isEnabled,
                    'visible' => $isEnabled,
                    'status' => $isEnabled ? 'visible' : 'hidden',
                    'allowed' => Layout::allowedFor('navbarLayout'),
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
            'success' => true,
            'module' => 'home',
            'activeCount' => $activeCount,
            'totalCount' => count($sections),
            'items' => $home->getDetailedList(),
            'sections' => $sections,
            'data' => [
                'layout' => [
                    'key' => 'homePageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed' => Layout::allowedFor('homePageLayout'),
                ],
                'activeCount' => $activeCount,
                'totalCount' => count($sections),
                'items' => $home->getDetailedList(),
                'sections' => $sections,
            ],
        ], 200);
    }

    /**
     * Module: About Page
     * GET /api/admin/modules/about
     */
    public function getAbout(): JsonResponse
    {
        $layoutRecord = Layout::find('aboutPageLayout');

        return response()->json([
            'success' => true,
            'module' => 'about',
            'data' => [
                'layout' => [
                    'key' => 'aboutPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed' => Layout::allowedFor('aboutPageLayout'),
                    'templates' => Layout::templatesFor('aboutPageLayout'),
                ],
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
            'module' => 'services',
            'data' => [
                'layout' => [
                    'key' => 'servicesPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed' => Layout::allowedFor('servicesPageLayout'),
                ],
                'total' => $services->count(),
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
            'module' => 'service-detail',
            'data' => [
                'layout' => [
                    'key' => 'serviceDetailPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed' => Layout::allowedFor('serviceDetailPageLayout'),
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
            'module' => 'portfolio',
            'data' => [
                'layout' => [
                    'key' => 'portfolioPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed' => Layout::allowedFor('portfolioPageLayout'),
                ],
                'total' => $portfolio->count(),
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
            'module' => 'portfolio-detail',
            'data' => [
                'layout' => [
                    'key' => 'portfolioDetailPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed' => Layout::allowedFor('portfolioDetailPageLayout'),
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
            'module' => 'blog',
            'data' => [
                'layout' => [
                    'key' => 'blogPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed' => Layout::allowedFor('blogPageLayout'),
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
            'module' => 'blog-detail',
            'data' => [
                'layout' => [
                    'key' => 'blogDetailPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed' => Layout::allowedFor('blogDetailPageLayout'),
                ],
                'defaultSeo' => $defaultSeo,
            ],
        ], 200);
    }

    /**
     * Module 7: Events Feed & Catalog
     * GET /api/admin/modules/events
     */
    public function getEvents(Request $request): JsonResponse
    {
        $layoutRecord = Layout::find('eventsPageLayout');
        $events = Event::orderBy('date', 'desc')->orderBy('id', 'desc')->get()->map(fn ($e) => $e->toAdminPayload());

        return response()->json([
            'success' => true,
            'module' => 'events',
            'data' => [
                'layout' => [
                    'key' => 'eventsPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed' => Layout::allowedFor('eventsPageLayout'),
                ],
                'total' => $events->count(),
                'events' => $events,
            ],
        ], 200);
    }

    /**
     * Module 7.1: Event Detail
     * GET /api/admin/modules/event-detail
     */
    public function getEventDetail(): JsonResponse
    {
        $layoutRecord = Layout::find('eventDetailPageLayout');
        $defaultSeo = PageSeo::find('event-detail')?->toPayload();

        return response()->json([
            'success' => true,
            'module' => 'event-detail',
            'data' => [
                'layout' => [
                    'key' => 'eventDetailPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed' => Layout::allowedFor('eventDetailPageLayout'),
                ],
                'defaultSeo' => $defaultSeo,
            ],
        ], 200);
    }

    /**
     * Module 8: Team Members
     * GET /api/admin/modules/team
     */
    public function getTeam(Request $request): JsonResponse
    {
        $layoutRecord = Layout::find('teamPageLayout');
        $members = TeamMember::orderBy('order', 'asc')->orderBy('id', 'asc')->get()->map(fn ($m) => $m->toAdminPayload());

        return response()->json([
            'success' => true,
            'module' => 'team',
            'data' => [
                'layout' => [
                    'key' => 'teamPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed' => Layout::allowedFor('teamPageLayout'),
                ],
                'total' => $members->count(),
                'members' => $members,
            ],
        ], 200);
    }

    // ─────────────────────────────────────────────────────────────
    // Contact Page API (Module 11)
    // ─────────────────────────────────────────────────────────────
    /**
     * Module 11: Contact Page and Inquiry Forms
     * GET /api/admin/modules/contact & /api/admin/contact
     */
    public function getContact(): JsonResponse
    {
        $layoutRecord = Layout::find('contactPageLayout');
        $currentTemplate = $layoutRecord?->templateId ?? 'layout1';
        $setting = SiteSetting::getSingleton();
        $defaultSeo = PageSeo::find('contact')?->toPayload() ?? PageSeo::defaults('contact');

        $templatesMeta = Layout::templatesFor('contactPageLayout');
        $templates = [];
        foreach ($templatesMeta as $id => $name) {
            $templates[] = [
                'id' => $id,
                'name' => $name,
                'active' => $id === $currentTemplate,
            ];
        }

        $counts = [
            'total' => Inquiry::count(),
            'new' => Inquiry::where('status', 'new')->count(),
            'read' => Inquiry::where('status', 'read')->count(),
            'replied' => Inquiry::where('status', 'replied')->count(),
            'archived' => Inquiry::where('status', 'archived')->count(),
        ];

        return response()->json([
            'success' => true,
            'module' => 'contact',
            'data' => [
                'layout' => [
                    'key' => 'contactPageLayout',
                    'templateId' => $currentTemplate,
                    'allowed' => Layout::allowedFor('contactPageLayout'),
                    'templates' => $templates,
                ],
                'seo' => $defaultSeo,
                'contactInfo' => $setting->toContactInfo(),
                'siteInfo' => $setting->toContactInfo(),
                'stats' => $counts,
                'recent' => Inquiry::orderBy('id', 'desc')->take(5)->get()->map->toPayload(),
            ],
        ], 200);
    }

    /**
     * Module 9: Testimonials and Reviews
     * GET /api/admin/modules/testimonials
     */
    public function getTestimonials(Request $request): JsonResponse
    {
        $layoutRecord = Layout::find('testimonialsPageLayout');
        $testimonials = Testimonial::orderBy('order', 'asc')->orderBy('id', 'asc')->get()->map(fn ($t) => $t->toAdminPayload());

        return response()->json([
            'success' => true,
            'module' => 'testimonials',
            'data' => [
                'layout' => [
                    'key' => 'testimonialsPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed' => Layout::allowedFor('testimonialsPageLayout'),
                ],
                'total' => $testimonials->count(),
                'testimonials' => $testimonials,
            ],
        ], 200);
    }

    /**
     * Module 10: Gallery and Media
     * GET /api/admin/modules/gallery
     */
    public function getGallery(Request $request): JsonResponse
    {
        $layoutRecord = Layout::find('galleryPageLayout');
        $albums = Gallery::orderBy('order', 'asc')->orderBy('id', 'asc')->get()->map(fn ($g) => $g->toAdminPayload());

        return response()->json([
            'success' => true,
            'module' => 'gallery',
            'data' => [
                'layout' => [
                    'key' => 'galleryPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed' => Layout::allowedFor('galleryPageLayout'),
                ],
                'total' => $albums->count(),
                'albums' => $albums,
            ],
        ], 200);
    }

    /**
     * Module 13: Careers (Job Listings)
     * GET /api/admin/modules/career
     */
    public function getCareer(Request $request): JsonResponse
    {
        $layoutRecord = Layout::find('careerPageLayout');
        $careers = Career::orderBy('order', 'asc')->orderBy('id', 'desc')->get()->map(fn ($c) => $c->toAdminPayload());
        $departments = Career::distinct()->whereNotNull('department')->pluck('department')->values();

        return response()->json([
            'success' => true,
            'module' => 'career',
            'data' => [
                'layout' => [
                    'key' => 'careerPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed' => Layout::allowedFor('careerPageLayout'),
                ],
                'total' => $careers->count(),
                'openCount' => Career::open()->count(),
                'departments' => $departments,
                'openings' => $careers,
            ],
        ], 200);
    }

    /**
     * Module 14: FAQs
     * GET /api/admin/modules/faq
     */
    public function getFaq(Request $request): JsonResponse
    {
        $layoutRecord = Layout::find('faqPageLayout');
        $faqs = Faq::orderBy('order', 'asc')->orderBy('id', 'asc')->get()->map(fn ($f) => $f->toAdminPayload());
        $categories = Faq::distinct()->whereNotNull('category')->pluck('category')->values();

        return response()->json([
            'success' => true,
            'module' => 'faq',
            'data' => [
                'layout' => [
                    'key' => 'faqPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed' => Layout::allowedFor('faqPageLayout'),
                ],
                'total' => $faqs->count(),
                'categories' => $categories,
                'faqs' => $faqs,
            ],
        ], 200);
    }

    /**
     * Module 15: Terms of Service
     * GET /api/admin/modules/terms
     */
    public function getTerms(): JsonResponse
    {
        $layoutRecord = Layout::find('termsPageLayout');
        $terms = LegalPage::firstOrCreate(
            ['type' => 'terms'],
            [
                'title' => 'Terms of Service',
                'content' => '<h2>1. Acceptance of Terms</h2><p>By accessing or using our services, you agree to be bound by these Terms.</p>',
                'lastUpdated' => now()->format('F Y'),
            ]
        );

        return response()->json([
            'success' => true,
            'module' => 'terms',
            'data' => [
                'layout' => [
                    'key' => 'termsPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed' => Layout::allowedFor('termsPageLayout'),
                ],
                'page' => $terms->toPayload(),
            ],
        ], 200);
    }

    /**
     * Module 16: Privacy Policy
     * GET /api/admin/modules/privacy
     */
    public function getPrivacy(): JsonResponse
    {
        $layoutRecord = Layout::find('privacyPageLayout');
        $privacy = LegalPage::firstOrCreate(
            ['type' => 'privacy'],
            [
                'title' => 'Privacy Policy',
                'content' => '<h2>1. Information We Collect</h2><p>We respect your privacy and protect personal data.</p>',
                'lastUpdated' => now()->format('F Y'),
            ]
        );

        return response()->json([
            'success' => true,
            'module' => 'privacy',
            'data' => [
                'layout' => [
                    'key' => 'privacyPageLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed' => Layout::allowedFor('privacyPageLayout'),
                ],
                'page' => $privacy->toPayload(),
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
            'module' => 'settings',
            'data' => $setting->toPayload(),
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
        $active = [];

        $navbar = NavbarConfig::getSingleton();
        $currNavLayout = $records->get('navbarLayout')?->templateId ?? 'layout1';
        $navBehavior = $navbar->getBehaviorForLayout($currNavLayout);

        foreach (Layout::$modules as $key => $meta) {
            $current = $records->get($key)?->templateId ?? 'layout1';

            if ($key === 'navbarLayout') {
                $isEnabled = $navbar->isNavbarEnabled();
                $navItems = is_array($navbar->items) ? array_values($navbar->items) : NavbarConfig::defaultItems();
                $active[$key] = [
                    'layout' => $current,
                    'behavior' => $navBehavior,
                    'enabled' => $isEnabled,
                    'visible' => $isEnabled,
                    'status' => $isEnabled ? 'visible' : 'hidden',
                    'items' => $navItems,
                ];
            } elseif ($key === 'topbarLayout') {
                $isTopbarEnabled = \App\Http\Controllers\TopbarController::isTopbarEnabled();
                $active[$key] = [
                    'layout'   => $isTopbarEnabled ? ($current === 'layout0' ? 'layout1' : $current) : 'layout0',
                    'enabled'  => $isTopbarEnabled,
                    'visible'  => $isTopbarEnabled,
                    'status'   => $isTopbarEnabled ? 'visible' : 'hidden',
                ];
            } elseif ($key === 'footerLayout') {
                $footer = \App\Models\FooterConfig::getSingleton();
                $footerItems = $footer->getItemsList();
                $active[$key] = [
                    'layout'     => $current,
                    'enabled'    => true,
                    'visible'    => true,
                    'status'     => 'visible',
                    'items'      => $footerItems,
                    'links'      => $footerItems,
                    'grid'       => is_array($footer->grid) ? $footer->grid : \App\Models\FooterConfig::defaultGrid(),
                    'cta'        => is_array($footer->cta) ? $footer->cta : \App\Models\FooterConfig::defaultCta(),
                    'customCode' => is_array($footer->customCode) ? $footer->customCode : \App\Models\FooterConfig::defaultCustomCode(),
                ];
            } else {
                $active[$key] = $current;
            }

            $modules[] = [
                'key' => $key,
                'name' => $meta['name'],
                'templateId' => $current,
                'allowed' => Layout::allowedFor($key),
                'updatedAt' => $records->get($key)?->updatedAt,
            ];
        }

        return response()->json([
            'success' => true,
            'module' => 'layouts',
            'data' => [
                'active' => $active,
                'modules' => $modules,
            ],
        ], 200);
    }

    /**
     * Module 12: Footer
     * GET /api/admin/modules/footer
     */
    public function getFooter(): JsonResponse
    {
        $layoutRecord = Layout::find('footerLayout');
        $footer = \App\Models\FooterConfig::getSingleton();

        return response()->json([
            'success' => true,
            'module'  => 'footer',
            'data'    => array_merge($footer->toAdminPayload(), [
                'layout' => [
                    'key'        => 'footerLayout',
                    'templateId' => $layoutRecord?->templateId ?? 'layout1',
                    'allowed'    => Layout::allowedFor('footerLayout'),
                ],
            ]),
        ], 200);
    }
}
