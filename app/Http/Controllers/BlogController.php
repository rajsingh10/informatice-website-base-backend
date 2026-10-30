<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Public feed of published blog posts.
     * Supports ?category= and ?tag= filters.
     * Sorts by date descending and hides future-dated posts.
     */
    public function indexPublic(Request $request): JsonResponse
    {
        $query = Blog::where('status', 'published')
            ->where('date', '<=', now()->toDateString());

        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        if ($request->has('featured') || $request->has('isFeatured')) {
            $query->where('isFeatured', filter_var($request->query('featured', $request->query('isFeatured')), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('tag')) {
            $tag = $request->query('tag');
            $query->whereJsonContains('tags', $tag);
        }

        $total = $query->count();

        $query->orderBy('date', 'desc')->orderBy('id', 'desc');

        if ($request->filled('limit')) {
            $limit = max(1, (int) $request->query('limit', 10));
            $page  = max(1, (int) $request->query('page', 1));
            $posts = $query->forPage($page, $limit)->get();
        } else {
            $posts = $query->get();
        }

        return response()->json([
            'success' => true,
            'total'   => $total,
            'data'    => $posts->map(fn ($post) => $post->toListPayload()),
        ]);
    }

    /**
     * Public single article by slug.
     */
    public function showPublic(string $slug): JsonResponse
    {
        $post = Blog::where('slug', $slug)
            ->where('status', 'published')
            ->where('date', '<=', now()->toDateString())
            ->first();

        if (! $post) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found or not yet published.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $post->toDetailPayload(),
        ]);
    }

    /**
     * Admin list of all blog posts (including draft & scheduled).
     */
    public function indexAdmin(Request $request): JsonResponse
    {
        $query = Blog::query();

        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        if ($request->has('featured') || $request->has('isFeatured')) {
            $query->where('isFeatured', filter_var($request->query('featured', $request->query('isFeatured')), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('search')) {
            $search = '%' . $request->query('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('excerpt', 'like', $search)
                  ->orWhere('content', 'like', $search);
            });
        }

        $total = $query->count();
        $query->orderBy('date', 'desc')->orderBy('id', 'desc');

        if ($request->filled('limit')) {
            $limit = max(1, (int) $request->query('limit', 20));
            $page  = max(1, (int) $request->query('page', 1));
            $posts = $query->forPage($page, $limit)->get();
        } else {
            $posts = $query->get();
        }

        return response()->json([
            'success' => true,
            'total'   => $total,
            'data'    => $posts->map(fn ($post) => $post->toAdminPayload()),
        ]);
    }

    /**
     * Admin single blog post for edit form.
     */
    public function showAdmin(int $id): JsonResponse
    {
        $post = Blog::find($id);

        if (! $post) {
            return response()->json([
                'success' => false,
                'message' => 'Blog post not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $post->toAdminPayload(),
        ]);
    }

    /**
     * Admin create a new blog post.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'slug'               => 'nullable|string|max:255|unique:blogs,slug',
            'excerpt'            => 'nullable|string',
            'content'            => 'nullable|string',
            'image'              => 'nullable|string|max:1000',
            'imageAlt'           => 'nullable|string|max:500',
            'category'           => 'nullable|string|max:100',
            'author'             => 'nullable|array',
            'author.name'        => 'nullable|string|max:150',
            'author.designation' => 'nullable|string|max:150',
            'author.avatar'      => 'nullable|string|max:1000',
            'date'               => 'nullable|date_format:Y-m-d',
            'readTime'           => 'nullable|string|max:50',
            'tags'               => 'nullable|array',
            'tags.*'             => 'string|max:50',
            'isFeatured'         => 'nullable|boolean',
            'status'             => 'nullable|in:published,draft,scheduled',
            'seo'                => 'nullable|array',
        ]);

        // Auto-generate slug if not provided
        $slug = ! empty($validated['slug'])
            ? Blog::generateSlug($validated['slug'])
            : Blog::generateSlug($validated['title']);

        // Auto-calculate readTime if not provided
        $readTime = ! empty($validated['readTime'])
            ? $validated['readTime']
            : Blog::calculateReadTime($validated['content'] ?? '');

        // Default author structure
        $author = [
            'name'        => $validated['author']['name'] ?? 'Admin',
            'designation' => $validated['author']['designation'] ?? 'Author',
            'avatar'      => $validated['author']['avatar'] ?? '',
        ];

        $createData = [
            'title'    => $validated['title'],
            'slug'     => $slug,
            'excerpt'  => $validated['excerpt'] ?? null,
            'content'  => $validated['content'] ?? null,
            'image'    => $validated['image'] ?? null,
            'category' => $validated['category'] ?? 'General',
            'author'   => $author,
            'date'     => $validated['date'] ?? now()->toDateString(),
            'readTime' => $readTime,
            'tags'     => $validated['tags'] ?? [],
            'isFeatured' => $request->has('isFeatured') ? filter_var($request->input('isFeatured'), FILTER_VALIDATE_BOOLEAN) : false,
            'status'   => $validated['status'] ?? 'published',
            'seo'      => $validated['seo'] ?? [],
        ];

        $imageAlt = (string) ($request->input('imageAlt', $request->input('altImage', '')));
        if (\Illuminate\Support\Facades\Schema::hasColumn('blogs', 'imageAlt')) {
            $createData['imageAlt'] = $imageAlt;
        } else {
            $seo = is_array($createData['seo']) ? $createData['seo'] : [];
            $seo['imageAlt'] = $imageAlt;
            $createData['seo'] = $seo;
        }

        $post = Blog::create($createData);

        return response()->json([
            'success' => true,
            'message' => "Blog post '{$post->title}' created successfully.",
            'data'    => $post->toAdminPayload(),
        ], 201);
    }

    /**
     * Admin update an existing blog post.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $post = Blog::find($id);

        if (! $post) {
            return response()->json([
                'success' => false,
                'message' => 'Blog post not found.',
            ], 404);
        }

        $validated = $request->validate([
            'title'              => 'sometimes|required|string|max:255',
            'slug'               => 'nullable|string|max:255|unique:blogs,slug,' . $id,
            'excerpt'            => 'nullable|string',
            'content'            => 'nullable|string',
            'image'              => 'nullable|string|max:1000',
            'imageAlt'           => 'nullable|string|max:500',
            'category'           => 'nullable|string|max:100',
            'author'             => 'nullable|array',
            'author.name'        => 'nullable|string|max:150',
            'author.designation' => 'nullable|string|max:150',
            'author.avatar'      => 'nullable|string|max:1000',
            'date'               => 'nullable|date_format:Y-m-d',
            'readTime'           => 'nullable|string|max:50',
            'tags'               => 'nullable|array',
            'tags.*'             => 'string|max:50',
            'isFeatured'         => 'nullable|boolean',
            'status'             => 'nullable|in:published,draft,scheduled',
            'seo'                => 'nullable|array',
        ]);

        if (isset($validated['title'])) {
            $post->title = $validated['title'];
        }

        if (array_key_exists('slug', $validated) && ! empty($validated['slug'])) {
            $post->slug = Blog::generateSlug($validated['slug'], $id);
        }

        if (array_key_exists('excerpt', $validated)) {
            $post->excerpt = $validated['excerpt'];
        }

        if (array_key_exists('content', $validated)) {
            $post->content = $validated['content'];
            // Recompute readTime if not explicitly passed
            if (empty($validated['readTime'])) {
                $post->readTime = Blog::calculateReadTime($post->content);
            }
        }

        if (array_key_exists('image', $validated)) {
            $post->image = $validated['image'];
        }

        if ($request->has('imageAlt') || $request->has('altImage')) {
            $imageAlt = (string) $request->input('imageAlt', $request->input('altImage', ''));
            if (\Illuminate\Support\Facades\Schema::hasColumn('blogs', 'imageAlt')) {
                $post->imageAlt = $imageAlt;
            } else {
                $seo = is_array($post->seo) ? $post->seo : [];
                $seo['imageAlt'] = $imageAlt;
                $post->seo = $seo;
            }
        }

        if (array_key_exists('category', $validated)) {
            $post->category = $validated['category'] ?: 'General';
        }

        if (array_key_exists('author', $validated) && is_array($validated['author'])) {
            $currentAuthor = is_array($post->author) ? $post->author : [];
            $post->author = [
                'name'        => $validated['author']['name'] ?? ($currentAuthor['name'] ?? 'Admin'),
                'designation' => $validated['author']['designation'] ?? ($currentAuthor['designation'] ?? 'Author'),
                'avatar'      => $validated['author']['avatar'] ?? ($currentAuthor['avatar'] ?? ''),
            ];
        }

        if (array_key_exists('date', $validated) && ! empty($validated['date'])) {
            $post->date = $validated['date'];
        }

        if (array_key_exists('readTime', $validated) && ! empty($validated['readTime'])) {
            $post->readTime = $validated['readTime'];
        }

        if (array_key_exists('tags', $validated)) {
            $post->tags = is_array($validated['tags']) ? array_values($validated['tags']) : [];
        }

        if (array_key_exists('status', $validated)) {
            $post->status = $validated['status'];
        }

        if ($request->has('isFeatured')) {
            $post->isFeatured = filter_var($request->input('isFeatured'), FILTER_VALIDATE_BOOLEAN);
        }

        if (array_key_exists('seo', $validated)) {
            $post->seo = is_array($validated['seo']) ? $validated['seo'] : [];
        }

        $post->save();

        return response()->json([
            'success' => true,
            'message' => "Blog post '{$post->title}' updated successfully.",
            'data'    => $post->toAdminPayload(),
        ]);
    }

    /**
     * Admin delete a blog post.
     */
    public function destroy(int $id): JsonResponse
    {
        $post = Blog::find($id);

        if (! $post) {
            return response()->json([
                'success' => false,
                'message' => 'Blog post not found.',
            ], 404);
        }

        $title = $post->title;
        $post->delete();

        return response()->json([
            'success' => true,
            'message' => "Blog post '{$title}' deleted successfully.",
        ]);
    }
}

