<?php

namespace App\Http\Controllers;

use App\Mail\NewInquiryNotification;
use App\Models\Inquiry;
use App\Models\Layout;
use App\Models\PageSeo;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Module 11: Contact Page & Inquiry Forms API Controller
 * Handles visitor inquiry submissions, admin inquiries CRUD, and contact info management.
 */
class InquiryController extends Controller
{
    /**
     * GET /public/contact & /api/public/contact — No Auth
     * Returns public contact page configuration: active layout, site contact info,
     * SEO metadata, and published services for the inquiry form dropdown.
     */
    public function getPublicContact(): JsonResponse
    {
        $layoutRecord = Layout::find('contactPageLayout');
        $currentTemplate = $layoutRecord?->templateId ?? 'layout1';
        $setting = SiteSetting::getSingleton();
        $seo = PageSeo::find('contact')?->toPayload() ?? PageSeo::defaults('contact');

        $templates = Layout::templatesFor('contactPageLayout');
        $templateName = $templates[$currentTemplate] ?? 'Split Form with Google Map';

        $availableServices = Service::where('isPublished', true)
            ->orderBy('order')
            ->orderBy('id')
            ->get(['id', 'title', 'slug', 'category']);

        return response()->json([
            'success' => true,
            'data'    => [
                'layout' => [
                    'key'          => 'contactPageLayout',
                    'templateId'   => $currentTemplate,
                    'templateName' => $templateName,
                ],
                'contactInfo' => $setting->toContactInfo(),
                'siteInfo'    => $setting->toContactInfo(),
                'seo'         => $seo,
                'services'    => $availableServices,
            ],
        ], 200);
    }

    /**
     * PUT|POST /admin/contact & /api/admin/contact & /admin/modules/contact — Auth Required
     * Update contact information (email, phone, address, mapEmbedUrl, offices, socials).
     */
    public function updateContactInfo(Request $request): JsonResponse
    {
        $input = $request->all();

        // If contactInfo is nested inside an object, unpack it
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

        // Auto-decode JSON strings for offices and socials
        if (isset($input['offices']) && is_string($input['offices'])) {
            $decoded = json_decode($input['offices'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $input['offices'] = $decoded;
            }
        }
        if (isset($input['socials']) && is_string($input['socials'])) {
            $decoded = json_decode($input['socials'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $input['socials'] = $decoded;
            }
        }

        $validator = validator($input, [
            'email'       => 'nullable|string|max:255',
            'phone'       => 'nullable|string|max:50',
            'address'     => 'nullable|string',
            'mapEmbedUrl' => 'nullable|string',
            'offices'     => 'nullable|array',
            'socials'     => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $setting = SiteSetting::getSingleton();

        $fillData = [];
        $fields = ['email', 'phone', 'address', 'mapEmbedUrl', 'offices', 'socials'];
        foreach ($fields as $field) {
            if (array_key_exists($field, $input)) {
                $fillData[$field] = $input[$field];
            }
        }

        if (array_key_exists('map_embed_url', $input) && ! array_key_exists('mapEmbedUrl', $fillData)) {
            $fillData['mapEmbedUrl'] = $input['map_embed_url'];
        }

        $setting->update($fillData);
        $setting->refresh();

        return response()->json([
            'success'     => true,
            'message'     => 'Contact information updated successfully.',
            'contactInfo' => $setting->toContactInfo(),
            'data'        => [
                'contactInfo' => $setting->toContactInfo(),
                'siteInfo'    => $setting->toContactInfo(),
            ],
        ], 200);
    }

    /**
     * POST /public/contact/inquiry & /api/public/contact/inquiry — No Auth
     * Visitor form submission.
     * Rules:
     * - name, email, message required.
     * - phone and serviceInterested optional.
     * - Email format and message length (max 2000) validated.
     * - website is a hidden honeypot field; if non-empty, return 201 but discard silently.
     * - Save to DB first, then send notification email to the admin address.
     * - Response 201: { "success": true, "message": "Thank you! Your message has been received." }
     */
    public function submitInquiry(Request $request): JsonResponse
    {
        // 1. Honeypot check: spambot field filled out
        if ($request->filled('website')) {
            Log::info('Inquiry honeypot triggered by IP: ' . $request->ip());
            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your message has been received.',
            ], 201);
        }

        // 2. Validate input fields
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255'],
            'message'           => ['required', 'string', 'max:2000'],
            'phone'             => ['nullable', 'string', 'max:50'],
            'serviceInterested' => ['nullable', 'string', 'max:255'],
            'service_interested'=> ['nullable', 'string', 'max:255'],
            'website'           => ['nullable', 'string'],
        ], [
            'name.required'    => 'The name field is required.',
            'email.required'   => 'The email field is required.',
            'email.email'      => 'The email must be a valid email address.',
            'message.required' => 'The message field is required.',
            'message.max'      => 'The message may not be greater than 2000 characters.',
        ]);

        $serviceInterested = $request->input('serviceInterested')
            ?? $request->input('service_interested')
            ?? $request->input('service');

        // 3. Save to database first
        $inquiry = Inquiry::create([
            'name'              => trim($validated['name']),
            'email'             => strtolower(trim($validated['email'])),
            'phone'             => $request->filled('phone') ? trim($request->input('phone')) : null,
            'serviceInterested' => ! empty($serviceInterested) ? trim($serviceInterested) : null,
            'message'           => trim($validated['message']),
            'status'            => Inquiry::STATUS_NEW,
            'ipAddress'         => $request->ip(),
            'userAgent'         => $request->userAgent(),
        ]);

        // 4. Send notification email to admin address (do not fail the request if email sending fails)
        try {
            $adminEmail = SiteSetting::getSingleton()->email;
            if (! $adminEmail) {
                $adminEmail = User::where('is_admin', true)->value('email')
                    ?? config('mail.from.address')
                    ?? 'admin@example.com';
            }

            if ($adminEmail) {
                Mail::to($adminEmail)->send(new NewInquiryNotification($inquiry));
            }
        } catch (\Throwable $e) {
            Log::warning('Inquiry notification email failed to send: ' . $e->getMessage());
        }

        // 5. Response 201 exactly as specified
        return response()->json([
            'success' => true,
            'message' => 'Thank you! Your message has been received.',
        ], 201);
    }

    /**
     * GET /admin/inquiries & /api/admin/inquiries — Auth Required
     * List inquiries with optional ?status= and ?q= filters and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $q      = $request->query('q', $request->query('search'));

        $query = Inquiry::query();

        if (! empty($status)) {
            $query->status($status);
        }

        if (! empty($q)) {
            $query->search($q);
        }

        $totalFiltered = (clone $query)->count();

        $limit = max(1, min(100, (int) $request->query('limit', $request->query('per_page', 20))));
        $page  = max(1, (int) $request->query('page', 1));
        $offset = ($page - 1) * $limit;

        $inquiries = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();

        $counts = [
            'all'      => Inquiry::count(),
            'new'      => Inquiry::where('status', 'new')->count(),
            'read'     => Inquiry::where('status', 'read')->count(),
            'replied'  => Inquiry::where('status', 'replied')->count(),
            'archived' => Inquiry::where('status', 'archived')->count(),
        ];

        return response()->json([
            'success' => true,
            'total'   => $totalFiltered,
            'page'    => $page,
            'limit'   => $limit,
            'counts'  => $counts,
            'data'    => $inquiries->map->toPayload(),
        ], 200);
    }

    /**
     * GET /admin/inquiries/:id & /api/admin/inquiries/:id — Auth Required
     * Retrieve a single inquiry by ID.
     */
    public function show(int $id): JsonResponse
    {
        $inquiry = Inquiry::find($id);

        if (! $inquiry) {
            return response()->json([
                'success' => false,
                'message' => "Inquiry #{$id} not found.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $inquiry->toPayload(),
        ], 200);
    }

    /**
     * PUT /admin/inquiries/:id & /api/admin/inquiries/:id — Auth Required
     * Update inquiry status (new, read, replied, archived) and optional admin notes.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $inquiry = Inquiry::find($id);

        if (! $inquiry) {
            return response()->json([
                'success' => false,
                'message' => "Inquiry #{$id} not found.",
            ], 404);
        }

        $allowedStatusesStr = implode(',', Inquiry::$allowedStatuses);

        $validated = $request->validate([
            'status'     => ['required', 'string', "in:{$allowedStatusesStr}"],
            'adminNotes' => ['nullable', 'string'],
        ], [
            'status.required' => 'The status field is required.',
            'status.in'       => "Status must be one of: {$allowedStatusesStr}.",
        ]);

        $inquiry->status = strtolower($validated['status']);

        if ($request->has('adminNotes')) {
            $inquiry->adminNotes = $request->input('adminNotes');
        }

        $inquiry->save();

        return response()->json([
            'success' => true,
            'message' => "Inquiry status updated to {$inquiry->status}.",
            'data'    => $inquiry->toPayload(),
        ], 200);
    }

    /**
     * DELETE /admin/inquiries/:id & /api/admin/inquiries/:id — Auth Required
     * Delete an inquiry by ID.
     */
    public function destroy(int $id): JsonResponse
    {
        $inquiry = Inquiry::find($id);

        if (! $inquiry) {
            return response()->json([
                'success' => false,
                'message' => "Inquiry #{$id} not found.",
            ], 404);
        }

        $inquiry->delete();

        return response()->json([
            'success' => true,
            'message' => "Inquiry #{$id} deleted successfully.",
        ], 200);
    }
}

