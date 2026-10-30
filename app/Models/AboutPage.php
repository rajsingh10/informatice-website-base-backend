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
        'teamSectionEnabled',
    ];

    protected $casts = [
        'values'             => 'array',
        'milestones'         => 'array',
        'stats'              => 'array',
        'images'             => 'array',
        'teamSectionEnabled' => 'boolean',
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
            'teamSectionEnabled' => true,
        ];
    }

    /**
     * Get or create the singleton About page record.
     */
    public static function getSingleton(): static
    {
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

        return $map;
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
            'images'             => is_array($this->images) ? $this->images : $defaults['images'],
            'imageAltMap'        => (object) $altMap,
            'teamSectionEnabled' => (bool) $this->teamSectionEnabled,
            'layout'             => $layoutData,
            'seo'                => $seoData,
            'updatedAt'          => $this->updated_at?->toISOString(),
        ];
    }
}

