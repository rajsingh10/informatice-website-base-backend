<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Career;
use App\Models\Event;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class FeaturedController extends Controller
{
    /**
     * GET /api/public/featured & /api/featured
     * Retrieve all featured items across all website modules (public view).
     * Optional query parameters:
     *   - module: Filter to a single module (e.g. ?module=services, ?module=portfolio, ?module=blogs, etc.)
     *   - limit: Limit the number of items returned per module (default: all)
     */
    public function indexPublic(Request $request): JsonResponse
    {
        $limit = $request->filled('limit') ? max(1, min(100, (int) $request->query('limit'))) : null;
        $requestedModule = strtolower(trim((string) $request->query('module', '')));

        $data = [];
        $counts = [];

        // 1. Services
        if (empty($requestedModule) || in_array($requestedModule, ['services', 'service'])) {
            $q = Service::where('isPublished', true);
            if (Schema::hasColumn('services', 'isFeatured')) {
                $q->where('isFeatured', true);
            }
            $q->orderBy('order', 'asc')->orderBy('id', 'asc');
            if ($limit) {
                $q->limit($limit);
            }
            $services = $q->get()->map->toCatalogPayload()->values();
            $data['services'] = $services;
            $counts['services'] = count($services);
        }

        // 2. Portfolio
        if (empty($requestedModule) || in_array($requestedModule, ['portfolio', 'portfolios', 'projects'])) {
            $q = Portfolio::where('isPublished', true);
            if (Schema::hasColumn('portfolios', 'isFeatured')) {
                $q->where('isFeatured', true);
            }
            $q->orderBy('order', 'asc')->orderBy('id', 'asc');
            if ($limit) {
                $q->limit($limit);
            }
            $portfolio = $q->get()->map->toListPayload()->values();
            $data['portfolio'] = $portfolio;
            $counts['portfolio'] = count($portfolio);
        }

        // 3. Blogs
        if (empty($requestedModule) || in_array($requestedModule, ['blogs', 'blog', 'posts'])) {
            $q = Blog::where('status', 'published');
            if (Schema::hasColumn('blogs', 'isFeatured')) {
                $q->where('isFeatured', true);
            }
            $q->orderBy('date', 'desc')->orderBy('id', 'desc');
            if ($limit) {
                $q->limit($limit);
            }
            $blogs = $q->get()->map->toListPayload()->values();
            $data['blogs'] = $blogs;
            $counts['blogs'] = count($blogs);
        }

        // 4. Events
        if (empty($requestedModule) || in_array($requestedModule, ['events', 'event'])) {
            $q = Event::where('status', 'published');
            if (Schema::hasColumn('events', 'isFeatured')) {
                $q->where('isFeatured', true);
            }
            $q->orderBy('date', 'asc')->orderBy('id', 'asc');
            if ($limit) {
                $q->limit($limit);
            }
            $events = $q->get()->map->toListPayload()->values();
            $data['events'] = $events;
            $counts['events'] = count($events);
        }

        // 5. Team
        if (empty($requestedModule) || in_array($requestedModule, ['team', 'team_members', 'members'])) {
            $q = TeamMember::where('isPublished', true);
            if (Schema::hasColumn('team_members', 'isFeatured')) {
                $q->where('isFeatured', true);
            }
            $q->orderBy('order', 'asc')->orderBy('id', 'asc');
            if ($limit) {
                $q->limit($limit);
            }
            $team = $q->get()->map->toPublicPayload()->values();
            $data['team'] = $team;
            $counts['team'] = count($team);
        }

        // 6. Testimonials
        if (empty($requestedModule) || in_array($requestedModule, ['testimonials', 'testimonial', 'reviews'])) {
            $q = Testimonial::where('isPublished', true);
            if (Schema::hasColumn('testimonials', 'isFeatured')) {
                $q->where('isFeatured', true);
            }
            $q->orderBy('order', 'asc')->orderBy('id', 'asc');
            if ($limit) {
                $q->limit($limit);
            }
            $testimonials = $q->get()->map->toPublicPayload()->values();
            $data['testimonials'] = $testimonials;
            $counts['testimonials'] = count($testimonials);
        }

        // 7. Gallery
        if (empty($requestedModule) || in_array($requestedModule, ['gallery', 'galleries', 'albums'])) {
            $q = Gallery::where('isPublished', true);
            if (Schema::hasColumn('galleries', 'isFeatured')) {
                $q->where('isFeatured', true);
            }
            $q->orderBy('order', 'asc')->orderBy('id', 'asc');
            if ($limit) {
                $q->limit($limit);
            }
            $gallery = $q->get()->map->toPublicPayload()->values();
            $data['gallery'] = $gallery;
            $counts['gallery'] = count($gallery);
        }

        // 8. Careers
        if (empty($requestedModule) || in_array($requestedModule, ['careers', 'career', 'jobs'])) {
            $q = Career::where('status', 'open');
            if (Schema::hasColumn('careers', 'isFeatured')) {
                $q->where('isFeatured', true);
            }
            $q->orderBy('order', 'asc')->orderBy('id', 'desc');
            if ($limit) {
                $q->limit($limit);
            }
            $careers = $q->get()->map->toPublicPayload()->values();
            $data['careers'] = $careers;
            $counts['careers'] = count($careers);
        }

        // 9. FAQs
        if (empty($requestedModule) || in_array($requestedModule, ['faqs', 'faq'])) {
            $q = Faq::published();
            if (Schema::hasColumn('faqs', 'isFeatured')) {
                $q->where('isFeatured', true);
            }
            $q->orderBy('order', 'asc')->orderBy('id', 'asc');
            if ($limit) {
                $q->limit($limit);
            }
            $faqs = $q->get()->map->toPublicPayload()->values();
            $data['faqs'] = $faqs;
            $counts['faqs'] = count($faqs);
        }

        $counts['total'] = array_sum($counts);

        return response()->json([
            'success' => true,
            'message' => 'Featured items retrieved successfully.',
            'counts'  => $counts,
            'data'    => $data,
        ], 200);
    }

    /**
     * GET /api/admin/featured & /api/admin/modules/featured
     * Admin view of all featured items across all modules (with full admin payload).
     */
    public function indexAdmin(Request $request): JsonResponse
    {
        $limit = $request->filled('limit') ? max(1, min(100, (int) $request->query('limit'))) : null;
        $requestedModule = strtolower(trim((string) $request->query('module', '')));

        $data = [];
        $counts = [];

        // 1. Services
        if (empty($requestedModule) || in_array($requestedModule, ['services', 'service'])) {
            $q = Service::query();
            if (Schema::hasColumn('services', 'isFeatured')) {
                $q->where('isFeatured', true);
            }
            $q->orderBy('order', 'asc')->orderBy('id', 'asc');
            if ($limit) {
                $q->limit($limit);
            }
            $services = $q->get()->map->toAdminPayload()->values();
            $data['services'] = $services;
            $counts['services'] = count($services);
        }

        // 2. Portfolio
        if (empty($requestedModule) || in_array($requestedModule, ['portfolio', 'portfolios', 'projects'])) {
            $q = Portfolio::query();
            if (Schema::hasColumn('portfolios', 'isFeatured')) {
                $q->where('isFeatured', true);
            }
            $q->orderBy('order', 'asc')->orderBy('id', 'asc');
            if ($limit) {
                $q->limit($limit);
            }
            $portfolio = $q->get()->map->toAdminPayload()->values();
            $data['portfolio'] = $portfolio;
            $counts['portfolio'] = count($portfolio);
        }

        // 3. Blogs
        if (empty($requestedModule) || in_array($requestedModule, ['blogs', 'blog', 'posts'])) {
            $q = Blog::query();
            if (Schema::hasColumn('blogs', 'isFeatured')) {
                $q->where('isFeatured', true);
            }
            $q->orderBy('date', 'desc')->orderBy('id', 'desc');
            if ($limit) {
                $q->limit($limit);
            }
            $blogs = $q->get()->map->toAdminPayload()->values();
            $data['blogs'] = $blogs;
            $counts['blogs'] = count($blogs);
        }

        // 4. Events
        if (empty($requestedModule) || in_array($requestedModule, ['events', 'event'])) {
            $q = Event::query();
            if (Schema::hasColumn('events', 'isFeatured')) {
                $q->where('isFeatured', true);
            }
            $q->orderBy('date', 'asc')->orderBy('id', 'asc');
            if ($limit) {
                $q->limit($limit);
            }
            $events = $q->get()->map->toAdminPayload()->values();
            $data['events'] = $events;
            $counts['events'] = count($events);
        }

        // 5. Team
        if (empty($requestedModule) || in_array($requestedModule, ['team', 'team_members', 'members'])) {
            $q = TeamMember::query();
            if (Schema::hasColumn('team_members', 'isFeatured')) {
                $q->where('isFeatured', true);
            }
            $q->orderBy('order', 'asc')->orderBy('id', 'asc');
            if ($limit) {
                $q->limit($limit);
            }
            $team = $q->get()->map->toAdminPayload()->values();
            $data['team'] = $team;
            $counts['team'] = count($team);
        }

        // 6. Testimonials
        if (empty($requestedModule) || in_array($requestedModule, ['testimonials', 'testimonial', 'reviews'])) {
            $q = Testimonial::query();
            if (Schema::hasColumn('testimonials', 'isFeatured')) {
                $q->where('isFeatured', true);
            }
            $q->orderBy('order', 'asc')->orderBy('id', 'asc');
            if ($limit) {
                $q->limit($limit);
            }
            $testimonials = $q->get()->map->toAdminPayload()->values();
            $data['testimonials'] = $testimonials;
            $counts['testimonials'] = count($testimonials);
        }

        // 7. Gallery
        if (empty($requestedModule) || in_array($requestedModule, ['gallery', 'galleries', 'albums'])) {
            $q = Gallery::query();
            if (Schema::hasColumn('galleries', 'isFeatured')) {
                $q->where('isFeatured', true);
            }
            $q->orderBy('order', 'asc')->orderBy('id', 'asc');
            if ($limit) {
                $q->limit($limit);
            }
            $gallery = $q->get()->map->toAdminPayload()->values();
            $data['gallery'] = $gallery;
            $counts['gallery'] = count($gallery);
        }

        // 8. Careers
        if (empty($requestedModule) || in_array($requestedModule, ['careers', 'career', 'jobs'])) {
            $q = Career::query();
            if (Schema::hasColumn('careers', 'isFeatured')) {
                $q->where('isFeatured', true);
            }
            $q->orderBy('order', 'asc')->orderBy('id', 'desc');
            if ($limit) {
                $q->limit($limit);
            }
            $careers = $q->get()->map->toAdminPayload()->values();
            $data['careers'] = $careers;
            $counts['careers'] = count($careers);
        }

        // 9. FAQs
        if (empty($requestedModule) || in_array($requestedModule, ['faqs', 'faq'])) {
            $q = Faq::query();
            if (Schema::hasColumn('faqs', 'isFeatured')) {
                $q->where('isFeatured', true);
            }
            $q->orderBy('order', 'asc')->orderBy('id', 'asc');
            if ($limit) {
                $q->limit($limit);
            }
            $faqs = $q->get()->map->toAdminPayload()->values();
            $data['faqs'] = $faqs;
            $counts['faqs'] = count($faqs);
        }

        $counts['total'] = array_sum($counts);

        return response()->json([
            'success' => true,
            'message' => 'Admin featured items retrieved successfully.',
            'counts'  => $counts,
            'data'    => $data,
        ], 200);
    }
}

