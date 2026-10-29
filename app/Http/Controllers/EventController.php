<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Public feed of events.
     * Supports ?when=upcoming (default), ?when=past, ?when=all.
     * Supports ?type=, ?search=, ?isOnline=, ?limit=, ?page=.
     * Only returns published events.
     */
    public function indexPublic(Request $request): JsonResponse
    {
        $query = Event::where('status', 'published');
        $today = now()->toDateString();
        $when = strtolower($request->query('when', 'upcoming'));

        if ($when === 'past') {
            $query->where(function ($q) use ($today) {
                $q->where(function ($sub) use ($today) {
                    $sub->whereNotNull('endDate')->where('endDate', '<', $today);
                })->orWhere(function ($sub) use ($today) {
                    $sub->whereNull('endDate')->where('date', '<', $today);
                });
            })->orderBy('date', 'desc')->orderBy('id', 'desc');
        } elseif ($when === 'all') {
            $query->orderBy('date', 'desc')->orderBy('id', 'desc');
        } else {
            // default 'upcoming'
            $query->where(function ($q) use ($today) {
                $q->where('date', '>=', $today)
                    ->orWhere(function ($sub) use ($today) {
                        $sub->whereNotNull('endDate')->where('endDate', '>=', $today);
                    });
            })->orderBy('date', 'asc')->orderBy('startTime', 'asc')->orderBy('id', 'asc');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        if ($request->has('isOnline')) {
            $query->where('isOnline', filter_var($request->query('isOnline'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $search = '%'.$request->query('search').'%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                    ->orWhere('shortDescription', 'like', $search)
                    ->orWhere('venue', 'like', $search)
                    ->orWhere('type', 'like', $search);
            });
        }

        $total = $query->count();

        if ($request->filled('limit')) {
            $limit = max(1, (int) $request->query('limit', 10));
            $page = max(1, (int) $request->query('page', 1));
            $events = $query->forPage($page, $limit)->get();
        } else {
            $events = $query->get();
        }

        return response()->json([
            'success' => true,
            'total' => $total,
            'data' => $events->map(fn ($event) => $event->toListPayload()),
        ]);
    }

    /**
     * Public single event detail by slug.
     */
    public function showPublic(string $slug): JsonResponse
    {
        $event = Event::where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (! $event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found or not yet published.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $event->toDetailPayload(),
        ]);
    }

    /**
     * Admin list of all events (all statuses, upcoming/past).
     */
    public function indexAdmin(Request $request): JsonResponse
    {
        $query = Event::query();
        $today = now()->toDateString();

        if ($request->filled('when')) {
            $when = strtolower($request->query('when'));
            if ($when === 'upcoming') {
                $query->where(function ($q) use ($today) {
                    $q->where('date', '>=', $today)
                        ->orWhere(function ($sub) use ($today) {
                            $sub->whereNotNull('endDate')->where('endDate', '>=', $today);
                        });
                });
            } elseif ($when === 'past') {
                $query->where(function ($q) use ($today) {
                    $q->where(function ($sub) use ($today) {
                        $sub->whereNotNull('endDate')->where('endDate', '<', $today);
                    })->orWhere(function ($sub) use ($today) {
                        $sub->whereNull('endDate')->where('date', '<', $today);
                    });
                });
            }
        }

        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->has('isOnline')) {
            $query->where('isOnline', filter_var($request->query('isOnline'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $search = '%'.$request->query('search').'%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                    ->orWhere('shortDescription', 'like', $search)
                    ->orWhere('longDescription', 'like', $search)
                    ->orWhere('venue', 'like', $search)
                    ->orWhere('type', 'like', $search);
            });
        }

        $total = $query->count();
        $query->orderBy('date', 'desc')->orderBy('id', 'desc');

        if ($request->filled('limit')) {
            $limit = max(1, (int) $request->query('limit', 20));
            $page = max(1, (int) $request->query('page', 1));
            $events = $query->forPage($page, $limit)->get();
        } else {
            $events = $query->get();
        }

        return response()->json([
            'success' => true,
            'total' => $total,
            'data' => $events->map(fn ($event) => $event->toAdminPayload()),
        ]);
    }

    /**
     * Admin single event by id.
     */
    public function showAdmin(int $id): JsonResponse
    {
        $event = Event::find($id);

        if (! $event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $event->toAdminPayload(),
        ]);
    }

    /**
     * Admin create a new event.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:events,slug',
            'type' => 'nullable|string|max:100',
            'shortDescription' => 'nullable|string',
            'longDescription' => 'nullable|string',
            'date' => 'required|date_format:Y-m-d',
            'endDate' => 'nullable|date_format:Y-m-d|after_or_equal:date',
            'startTime' => 'nullable|string|max:50',
            'endTime' => 'nullable|string|max:50',
            'venue' => 'nullable|string|max:500',
            'isOnline' => 'nullable|boolean',
            'onlineUrl' => 'nullable|string|max:1000',
            'price' => 'nullable|numeric|min:0',
            'totalSeats' => 'nullable|integer|min:0',
            'availableSeats' => 'nullable|integer|min:0',
            'agenda' => 'nullable|array',
            'agenda.*.time' => 'nullable|string|max:100',
            'agenda.*.topic' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:1000',
            'status' => 'nullable|in:published,draft,scheduled,archived',
            'seo' => 'nullable|array',
        ]);

        $isOnline = filter_var($validated['isOnline'] ?? false, FILTER_VALIDATE_BOOLEAN);

        // Constraint: When isOnline is true, onlineUrl is required
        if ($isOnline && empty($validated['onlineUrl'])) {
            return response()->json([
                'success' => false,
                'message' => 'onlineUrl is required when event is online.',
                'errors' => [
                    'onlineUrl' => ['The onlineUrl field is required when the event is online.'],
                ],
            ], 422);
        }

        $totalSeats = isset($validated['totalSeats']) ? (int) $validated['totalSeats'] : 0;
        $availableSeats = isset($validated['availableSeats']) ? (int) $validated['availableSeats'] : $totalSeats;

        // Constraint: availableSeats must not exceed totalSeats
        if ($availableSeats > $totalSeats) {
            return response()->json([
                'success' => false,
                'message' => 'availableSeats must not exceed totalSeats.',
                'errors' => [
                    'availableSeats' => ['The availableSeats cannot exceed totalSeats.'],
                ],
            ], 422);
        }

        // Auto-generate slug if not provided
        $slug = ! empty($validated['slug'])
            ? Event::generateSlug($validated['slug'])
            : Event::generateSlug($validated['title']);

        $event = Event::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'type' => $validated['type'] ?? 'Conference',
            'shortDescription' => $validated['shortDescription'] ?? null,
            'longDescription' => $validated['longDescription'] ?? null,
            'date' => $validated['date'],
            'endDate' => $validated['endDate'] ?? null,
            'startTime' => $validated['startTime'] ?? null,
            'endTime' => $validated['endTime'] ?? null,
            'venue' => $validated['venue'] ?? null,
            'isOnline' => $isOnline,
            'onlineUrl' => $isOnline ? ($validated['onlineUrl'] ?? '') : ($validated['onlineUrl'] ?? null),
            'price' => $validated['price'] ?? 0,
            'totalSeats' => $totalSeats,
            'availableSeats' => $availableSeats,
            'agenda' => $validated['agenda'] ?? [],
            'image' => $validated['image'] ?? null,
            'status' => $validated['status'] ?? 'published',
            'seo' => $validated['seo'] ?? [],
        ]);

        return response()->json([
            'success' => true,
            'message' => "Event '{$event->title}' created successfully.",
            'data' => $event->toAdminPayload(),
        ], 201);
    }

    /**
     * Admin update an existing event.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $event = Event::find($id);

        if (! $event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found.',
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:events,slug,'.$id,
            'type' => 'nullable|string|max:100',
            'shortDescription' => 'nullable|string',
            'longDescription' => 'nullable|string',
            'date' => 'sometimes|required|date_format:Y-m-d',
            'endDate' => 'nullable|date_format:Y-m-d',
            'startTime' => 'nullable|string|max:50',
            'endTime' => 'nullable|string|max:50',
            'venue' => 'nullable|string|max:500',
            'isOnline' => 'nullable|boolean',
            'onlineUrl' => 'nullable|string|max:1000',
            'price' => 'nullable|numeric|min:0',
            'totalSeats' => 'nullable|integer|min:0',
            'availableSeats' => 'nullable|integer|min:0',
            'agenda' => 'nullable|array',
            'agenda.*.time' => 'nullable|string|max:100',
            'agenda.*.topic' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:1000',
            'status' => 'nullable|in:published,draft,scheduled,archived',
            'seo' => 'nullable|array',
        ]);

        $startDate = $validated['date'] ?? $event->date->format('Y-m-d');
        $endDate = array_key_exists('endDate', $validated)
            ? $validated['endDate']
            : ($event->endDate ? $event->endDate->format('Y-m-d') : null);

        // Constraint: endDate must not be before date
        if (! empty($endDate) && $endDate < $startDate) {
            return response()->json([
                'success' => false,
                'message' => 'endDate must not be before date.',
                'errors' => [
                    'endDate' => ['The endDate must be a date after or equal to date.'],
                ],
            ], 422);
        }

        $isOnline = array_key_exists('isOnline', $validated)
            ? filter_var($validated['isOnline'], FILTER_VALIDATE_BOOLEAN)
            : (bool) $event->isOnline;

        $onlineUrl = array_key_exists('onlineUrl', $validated)
            ? $validated['onlineUrl']
            : $event->onlineUrl;

        // Constraint: When isOnline is true, onlineUrl is required
        if ($isOnline && empty($onlineUrl)) {
            return response()->json([
                'success' => false,
                'message' => 'onlineUrl is required when event is online.',
                'errors' => [
                    'onlineUrl' => ['The onlineUrl field is required when the event is online.'],
                ],
            ], 422);
        }

        $totalSeats = array_key_exists('totalSeats', $validated)
            ? (int) $validated['totalSeats']
            : (int) $event->totalSeats;

        $availableSeats = array_key_exists('availableSeats', $validated)
            ? (int) $validated['availableSeats']
            : (int) $event->availableSeats;

        // Constraint: availableSeats must not exceed totalSeats
        if ($availableSeats > $totalSeats) {
            return response()->json([
                'success' => false,
                'message' => 'availableSeats must not exceed totalSeats.',
                'errors' => [
                    'availableSeats' => ['The availableSeats cannot exceed totalSeats.'],
                ],
            ], 422);
        }

        if (array_key_exists('slug', $validated) && ! empty($validated['slug']) && $validated['slug'] !== $event->slug) {
            $validated['slug'] = Event::generateSlug($validated['slug'], $id);
        }

        $event->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Event '{$event->title}' updated successfully.",
            'data' => $event->fresh()->toAdminPayload(),
        ]);
    }

    /**
     * Admin delete an event.
     */
    public function destroy(int $id): JsonResponse
    {
        $event = Event::find($id);

        if (! $event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found.',
            ], 404);
        }

        $title = $event->title;
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => "Event '{$title}' deleted successfully.",
        ]);
    }
}
