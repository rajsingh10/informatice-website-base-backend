<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutPage extends Model
{
    protected $table = 'about_pages';

    protected $fillable = [
        'badge',
        'title',
        'tagline',
        'storyTitle',
        'storyContent',
        'heroImage',
        'heroImageAlt',
        'storyImage',
        'storyImageAlt',
        'mission',
        'vision',
        'values',
        'milestones',
        'stats',
        'images',
        'valuesImages',
        'differentiatorsImages',
        'teamSectionEnabled',
        'differentiatorsFeatured',
        'storyFeatured',
        'valuesFeatured',
    ];

    protected $casts = [
        'values'                  => 'array',
        'milestones'              => 'array',
        'stats'                   => 'array',
        'images'                  => 'array',
        'valuesImages'            => 'array',
        'differentiatorsImages'   => 'array',
        'teamSectionEnabled'      => 'boolean',
        'differentiatorsFeatured' => 'boolean',
        'storyFeatured'           => 'boolean',
        'valuesFeatured'          => 'boolean',
    ];

    /**
     * Default About page content payload.
     */
    public static function defaults(): array
    {
        return [
            'badge'              => 'About Us',
            'title'              => 'Driving Innovation & Sustainable Digital Growth',
            'tagline'            => 'Empowering forward-thinking enterprises with modern cloud architecture, intuitive experiences, and reliable engineering.',
            'storyTitle'         => 'A Decade of Engineering Excellence',
            'storyContent'       => 'Founded with a clear vision to bridge complex technical challenges with elegant digital experiences, our team combines deep software engineering expertise with design-thinking to craft solutions that scale effortlessly.',
            'heroImage'          => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1200&q=80',
            'heroImageAlt'       => 'NexGen engineering team collaborating on product architecture in a high-tech conference room',
            'storyImage'         => 'https://images.unsplash.com/photo-1553877522-43269d4ea984?auto=format&fit=crop&w=1000&q=80',
            'storyImageAlt'      => 'Software engineers reviewing system designs and strategic roadmaps on digital displays',
            'mission'            => 'To build transformative, secure, and scalable digital solutions that empower global businesses to succeed in an ever-evolving world.',
            'vision'             => 'To be the most trusted technology partner recognized globally for human-centric design, engineering integrity, and innovation.',
            'values'             => [
                [
                    'title'       => 'Engineering Excellence',
                    'description' => 'Uncompromising code quality, robust security, and cloud scalability.',
                    'icon'        => 'fa-solid fa-code',
                    'image'       => '',
                    'imageAlt'    => 'Clean code architecture and automated quality assurance',
                ],
                [
                    'title'       => 'Client Partnership',
                    'description' => 'Transparent communication, dedicated alignment, and mutual growth.',
                    'icon'        => 'fa-solid fa-handshake',
                    'image'       => '',
                    'imageAlt'    => 'Close collaborative partnership between our engineers and client stakeholders',
                ],
                [
                    'title'       => 'Integrity & Trust',
                    'description' => 'Ethical data governance, strict privacy standards, and dependable delivery.',
                    'icon'        => 'fa-solid fa-shield-halved',
                    'image'       => '',
                    'imageAlt'    => 'Enterprise security and reliable system governance',
                ],
                [
                    'title'       => 'Continuous Innovation',
                    'description' => 'Embracing next-gen AI, automation, and modern tech stacks.',
                    'icon'        => 'fa-solid fa-lightbulb',
                    'image'       => '',
                    'imageAlt'    => 'Research and development into modern AI and cloud architectures',
                ],
            ],
            'milestones'         => [
                [
                    'year'        => '2016',
                    'title'       => 'Founding Year',
                    'description' => 'Incorporate with a core team of 5 passionate software engineers.',
                    'image'       => '',
                    'imageAlt'    => 'Founding engineers in our original development hub',
                ],
                [
                    'year'        => '2019',
                    'title'       => 'Global Expansion',
                    'description' => 'Expanded international operations and enterprise delivery capabilities.',
                    'image'       => '',
                    'imageAlt'    => 'Opening of international engineering offices',
                ],
                [
                    'year'        => '2023',
                    'title'       => 'AI & Cloud Practice',
                    'description' => 'Launched specialized Next-Gen AI and Cloud modernization services.',
                    'image'       => '',
                    'imageAlt'    => 'Launch of our intelligent automation practice',
                ],
                [
                    'year'        => '2026',
                    'title'       => '250+ Enterprise Clients',
                    'description' => 'Serving over 250 enterprise brands across 18 countries.',
                    'image'       => '',
                    'imageAlt'    => 'Celebration of major enterprise milestone achievements',
                ],
            ],
            'stats'              => [
                ['label' => 'Years of Excellence', 'value' => '10+', 'suffix' => '+', 'icon' => 'fa-solid fa-calendar-check'],
                ['label' => 'Projects Delivered',   'value' => '250+', 'suffix' => '+', 'icon' => 'fa-solid fa-circle-check'],
                ['label' => 'Enterprise Clients',   'value' => '85+',  'suffix' => '+', 'icon' => 'fa-solid fa-building'],
                ['label' => 'Team Members',         'value' => '45+',  'suffix' => '+', 'icon' => 'fa-solid fa-users'],
            ],
            'images'             => [
                [
                    'slot' => 'img-about-hero',
                    'url'  => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1200&q=80',
                    'alt'  => 'NexGen engineering team collaborating on product architecture in a high-tech conference room',
                ],
                [
                    'slot' => 'img-about-story',
                    'url'  => 'https://images.unsplash.com/photo-1553877522-43269d4ea984?auto=format&fit=crop&w=1000&q=80',
                    'alt'  => 'Software engineers reviewing system designs and strategic roadmaps on digital displays',
                ],
                [
                    'slot' => 'img-about-culture',
                    'url'  => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=1000&q=80',
                    'alt'  => 'Team workshop fostering technical innovation and creative problem solving',
                ],
            ],
            'valuesImages' => [
                [
                    'url' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80',
                    'alt' => 'Team meeting',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1553877522-43269d4ea984?auto=format&fit=crop&w=800&q=80',
                    'alt' => 'Collaboration',
                ],
            ],
            'differentiatorsImages' => [
                [
                    'url' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=800&q=80',
                    'alt' => 'Client Success',
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=800&q=80',
                    'alt' => 'Agile Sprints',
                ],
            ],
            'teamSectionEnabled'      => true,
            'differentiatorsFeatured' => true,
            'storyFeatured'           => true,
            'valuesFeatured'          => true,
        ];
    }

    /**
     * Get or create the singleton About page record.
     */
    public static function getSingleton(): static
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('about_pages')) {
                if (! \Illuminate\Support\Facades\Schema::hasColumn('about_pages', 'valuesImages')) {
                    \Illuminate\Support\Facades\Schema::table('about_pages', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->longText('valuesImages')->nullable()->after('images');
                    });
                }
                if (! \Illuminate\Support\Facades\Schema::hasColumn('about_pages', 'differentiatorsImages')) {
                    \Illuminate\Support\Facades\Schema::table('about_pages', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->longText('differentiatorsImages')->nullable()->after('valuesImages');
                    });
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $about = static::first();
        if (! $about) {
            $about = static::create(static::defaults());
        }
        return $about;
    }

    /**
     * Extract a unified dictionary of image slot IDs => ALT texts.
     */
    public function getImageAltMap(): array
    {
        $map = [];

        if (! empty($this->heroImageAlt)) {
            $map['img-about-hero'] = (string) $this->heroImageAlt;
        }

        if (! empty($this->storyImageAlt)) {
            $map['img-about-story'] = (string) $this->storyImageAlt;
        }

        if (is_array($this->images)) {
            foreach ($this->images as $img) {
                if (is_array($img) && ! empty($img['slot']) && ! empty($img['alt'])) {
                    $map[(string) $img['slot']] = (string) $img['alt'];
                }
            }
        }

        if (is_array($this->valuesImages)) {
            foreach ($this->valuesImages as $i => $img) {
                if (is_array($img) && ! empty($img['alt'])) {
                    $slot = 'img-about-values-' . ($i + 1);
                    $map[$slot] = (string) $img['alt'];
                }
            }
        }

        if (is_array($this->differentiatorsImages)) {
            foreach ($this->differentiatorsImages as $i => $img) {
                if (is_array($img) && ! empty($img['alt'])) {
                    $slot = 'img-about-diff-' . ($i + 1);
                    $map[$slot] = (string) $img['alt'];
                }
            }
        }

        return $map;
    }

    /**
     * Get normalized multiple images array.
     */
    public function getNormalizedImages(): array
    {
        $raw = is_array($this->images) ? $this->images : static::defaults()['images'];
        $normalized = [];

        foreach ($raw as $index => $item) {
            $norm = static::normalizeImageItem($item, $index);
            if ($norm) {
                $normalized[] = $norm;
            }
        }

        return $normalized;
    }

    /**
     * Normalize a single image item.
     */
    public static function normalizeImageItem(mixed $item, int $index = 0): ?array
    {
        if (empty($item)) {
            return null;
        }

        if (is_string($item)) {
            $url = trim($item);
            if ($url === '') return null;
            return [
                'id'    => $index + 1,
                'slot'  => 'img-about-' . ($index + 1),
                'url'   => $url,
                'alt'   => '',
                'title' => '',
            ];
        }

        if (is_array($item)) {
            $url = (string) ($item['url'] ?? $item['image'] ?? $item['src'] ?? '');
            if (trim($url) === '') return null;
            $slot = (string) ($item['slot'] ?? ('img-about-' . ($index + 1)));
            $alt = (string) ($item['alt'] ?? $item['imageAlt'] ?? $item['caption'] ?? '');
            $title = (string) ($item['title'] ?? $item['name'] ?? '');
            $id = isset($item['id']) && is_numeric($item['id']) ? (int) $item['id'] : ($index + 1);

            return [
                'id'    => $id,
                'slot'  => $slot,
                'url'   => $url,
                'alt'   => $alt,
                'title' => $title,
            ];
        }

        return null;
    }

    /**
     * Normalize a gallery of image objects [{ url, alt }].
     */
    public static function normalizeImageGallery(mixed $items): array
    {
        if (empty($items)) {
            return [];
        }
        if (is_string($items)) {
            $decoded = json_decode($items, true);
            if (is_array($decoded)) {
                $items = $decoded;
            } else {
                $url = trim($items);
                return $url !== '' ? [['url' => $url, 'alt' => '']] : [];
            }
        }
        if (! is_array($items)) {
            return [];
        }
        $out = [];
        foreach ($items as $item) {
            if (is_string($item)) {
                $u = trim($item);
                if ($u !== '') {
                    $out[] = [
                        'url' => $u,
                        'alt' => '',
                    ];
                }
            } elseif (is_array($item)) {
                $u = (string) ($item['url'] ?? $item['image'] ?? $item['src'] ?? '');
                if (trim($u) !== '') {
                    $alt = (string) ($item['alt'] ?? $item['imageAlt'] ?? $item['caption'] ?? $item['title'] ?? '');
                    $out[] = [
                        'url' => $u,
                        'alt' => $alt,
                    ];
                }
            }
        }
        return array_values($out);
    }

    /**
     * Add a new image to the multiple images list.
     */
    public function addImage(array $imageData): array
    {
        $images = $this->getNormalizedImages();
        $nextId = 1;
        foreach ($images as $img) {
            if (isset($img['id']) && $img['id'] >= $nextId) {
                $nextId = $img['id'] + 1;
            }
        }

        $newImage = [
            'id'    => $nextId,
            'slot'  => ! empty($imageData['slot']) ? (string) $imageData['slot'] : ('img-about-' . $nextId),
            'url'   => (string) ($imageData['url'] ?? ''),
            'alt'   => (string) ($imageData['alt'] ?? $imageData['imageAlt'] ?? ''),
            'title' => (string) ($imageData['title'] ?? ''),
        ];

        $images[] = $newImage;
        $this->images = $images;
        $this->save();

        return $newImage;
    }

    /**
     * Update an existing image in the multiple images list by id, slot, or 0-based index.
     */
    public function updateImage(string|int $identifier, array $imageData): ?array
    {
        $images = $this->getNormalizedImages();
        $targetIndex = null;

        // Try matching by id or slot
        foreach ($images as $idx => $img) {
            if (isset($img['id']) && (string) $img['id'] === (string) $identifier) {
                $targetIndex = $idx;
                break;
            }
            if (isset($img['slot']) && $img['slot'] === (string) $identifier) {
                $targetIndex = $idx;
                break;
            }
        }

        // Fallback to array index if numeric and within range
        if ($targetIndex === null && is_numeric($identifier) && isset($images[(int) $identifier])) {
            $targetIndex = (int) $identifier;
        }

        if ($targetIndex === null) {
            return null;
        }

        $current = $images[$targetIndex];

        if (array_key_exists('url', $imageData)) {
            $current['url'] = (string) $imageData['url'];
        }
        if (array_key_exists('alt', $imageData)) {
            $current['alt'] = (string) $imageData['alt'];
        } elseif (array_key_exists('imageAlt', $imageData)) {
            $current['alt'] = (string) $imageData['imageAlt'];
        }
        if (array_key_exists('title', $imageData)) {
            $current['title'] = (string) $imageData['title'];
        }
        if (array_key_exists('slot', $imageData) && ! empty($imageData['slot'])) {
            $current['slot'] = (string) $imageData['slot'];
        }

        $images[$targetIndex] = $current;
        $this->images = $images;
        $this->save();

        return $current;
    }

    /**
     * Delete an existing image from the multiple images list.
     */
    public function deleteImage(string|int $identifier): bool
    {
        $images = $this->getNormalizedImages();
        $targetIndex = null;

        foreach ($images as $idx => $img) {
            if (isset($img['id']) && (string) $img['id'] === (string) $identifier) {
                $targetIndex = $idx;
                break;
            }
            if (isset($img['slot']) && $img['slot'] === (string) $identifier) {
                $targetIndex = $idx;
                break;
            }
        }

        if ($targetIndex === null && is_numeric($identifier) && isset($images[(int) $identifier])) {
            $targetIndex = (int) $identifier;
        }

        if ($targetIndex === null) {
            return false;
        }

        array_splice($images, $targetIndex, 1);
        $this->images = array_values($images);
        $this->save();

        return true;
    }

    /**
     * Format payload for public consumers.
     */
    public function toPayload(): array
    {
        $defaults = static::defaults();
        $altMap = $this->getImageAltMap();

        // Active layout
        $layoutRecord = Layout::find('aboutPageLayout');
        $layoutData = [
            'key'        => 'aboutPageLayout',
            'templateId' => $layoutRecord?->templateId ?? 'layout1',
            'allowed'    => Layout::allowedFor('aboutPageLayout'),
            'templates'  => Layout::templatesFor('aboutPageLayout'),
        ];

        // SEO metadata
        $pageSeo = PageSeo::find('about');
        $seoData = $pageSeo ? $pageSeo->toPayload() : PageSeo::defaults('about');
        if (empty($seoData['title'])) {
            $seoData['title'] = (string) ($this->title ?? 'About Us');
        }
        if (empty($seoData['description'])) {
            $seoData['description'] = (string) ($this->tagline ?? $defaults['tagline']);
        }
        // Merge imageAltMap into SEO data if SEO map is empty
        if (empty($seoData['imageAltMap']) || (is_object($seoData['imageAltMap']) && empty((array)$seoData['imageAltMap']))) {
            $seoData['imageAltMap'] = (object) $altMap;
        }

        return [
            'badge'              => (string) ($this->badge ?? $defaults['badge']),
            'title'              => (string) ($this->title ?? $defaults['title']),
            'tagline'            => (string) ($this->tagline ?? $defaults['tagline']),
            'storyTitle'         => (string) ($this->storyTitle ?? $defaults['storyTitle']),
            'storyContent'       => (string) ($this->storyContent ?? $defaults['storyContent']),
            // Structured hero image with direct alt attribute
            'heroImage'          => (string) ($this->heroImage ?? $defaults['heroImage']),
            'heroImageAlt'       => (string) ($this->heroImageAlt ?? $defaults['heroImageAlt']),
            'hero'               => [
                'url' => (string) ($this->heroImage ?? $defaults['heroImage']),
                'alt' => (string) ($this->heroImageAlt ?? $defaults['heroImageAlt']),
            ],
            // Structured story image with direct alt attribute
            'storyImage'         => (string) ($this->storyImage ?? $defaults['storyImage']),
            'storyImageAlt'      => (string) ($this->storyImageAlt ?? $defaults['storyImageAlt']),
            'story'              => [
                'url' => (string) ($this->storyImage ?? $defaults['storyImage']),
                'alt' => (string) ($this->storyImageAlt ?? $defaults['storyImageAlt']),
            ],
            'mission'            => (string) ($this->mission ?? $defaults['mission']),
            'vision'             => (string) ($this->vision ?? $defaults['vision']),
            'values'             => is_array($this->values) ? $this->values : $defaults['values'],
            'milestones'         => is_array($this->milestones) ? $this->milestones : $defaults['milestones'],
            'stats'              => is_array($this->stats) ? $this->stats : $defaults['stats'],
            'images'             => $this->getNormalizedImages(),
            'valuesImages'       => is_array($this->valuesImages) ? $this->valuesImages : (is_array($defaults['valuesImages'] ?? null) ? $defaults['valuesImages'] : []),
            'differentiatorsImages' => is_array($this->differentiatorsImages) ? $this->differentiatorsImages : (is_array($defaults['differentiatorsImages'] ?? null) ? $defaults['differentiatorsImages'] : []),
            'values_images'      => is_array($this->valuesImages) ? $this->valuesImages : (is_array($defaults['valuesImages'] ?? null) ? $defaults['valuesImages'] : []),
            'differentiators_images' => is_array($this->differentiatorsImages) ? $this->differentiatorsImages : (is_array($defaults['differentiatorsImages'] ?? null) ? $defaults['differentiatorsImages'] : []),
            'imageAltMap'             => (object) $altMap,
            'teamSectionEnabled'      => (bool) $this->teamSectionEnabled,
            'differentiatorsFeatured' => (bool) ($this->differentiatorsFeatured ?? true),
            'storyFeatured'           => (bool) ($this->storyFeatured ?? true),
            'valuesFeatured'          => (bool) ($this->valuesFeatured ?? true),
            'differentiators_featured'=> (bool) ($this->differentiatorsFeatured ?? true),
            'story_featured'          => (bool) ($this->storyFeatured ?? true),
            'values_featured'         => (bool) ($this->valuesFeatured ?? true),
            'layout'                  => $layoutData,
            'seo'                => $seoData,
            'updatedAt'          => $this->updated_at?->toISOString(),
        ];
    }
}

