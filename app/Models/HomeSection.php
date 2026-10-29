<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    protected $table = 'home_sections';

    protected $fillable = [
        'sections',
    ];

    protected $casts = [
        'sections' => 'array',
    ];

    /**
     * The 10 recognized home section IDs.
     */
    public static array $validSections = [
        'hero',
        'about',
        'services',
        'portfolio',
        'stats',
        'clients',
        'testimonials',
        'blog',
        'faq',
        'cta',
    ];

    /**
     * Metadata for all 10 toggleable home sections (matching frontend UI).
     */
    public static array $sectionMetadata = [
        'hero' => [
            'id'          => 'hero',
            'key'         => 'hero',
            'name'        => 'Hero Section / Slider / Video',
            'title'       => 'Hero Section / Slider / Video',
            'description' => 'Main header showcase, headline, call-to-action buttons, and visual media.',
        ],
        'about' => [
            'id'          => 'about',
            'key'         => 'about',
            'name'        => 'About & Brand Story Section',
            'title'       => 'About & Brand Story Section',
            'description' => 'Company introduction, mission, storyboards, and value differentiators.',
        ],
        'services' => [
            'id'          => 'services',
            'key'         => 'services',
            'name'        => 'Services & Solutions Showcase',
            'title'       => 'Services & Solutions Showcase',
            'description' => 'Feature grids, service cards, bento displays, and offering highlights.',
        ],
        'portfolio' => [
            'id'          => 'portfolio',
            'key'         => 'portfolio',
            'name'        => 'Portfolio & Case Studies Stack',
            'title'       => 'Portfolio & Case Studies Stack',
            'description' => 'Featured client projects, interactive tabs, screenshots, and case study previews.',
        ],
        'stats' => [
            'id'          => 'stats',
            'key'         => 'stats',
            'name'        => 'Stats, Key Metrics & Achievements',
            'title'       => 'Stats, Key Metrics & Achievements',
            'description' => 'Animated counters, company numbers, projects completed, and trust statistics.',
        ],
        'clients' => [
            'id'          => 'clients',
            'key'         => 'clients',
            'name'        => 'Client Logos & Marquee Strip',
            'title'       => 'Client Logos & Marquee Strip',
            'description' => 'Brand partner logos, curved loop text marquee, and corporate trust strip.',
        ],
        'testimonials' => [
            'id'          => 'testimonials',
            'key'         => 'testimonials',
            'name'        => 'Testimonials & Reviews Wall',
            'title'       => 'Testimonials & Reviews Wall',
            'description' => 'Client reviews, quote sliders, star ratings, and video feedback.',
        ],
        'blog' => [
            'id'          => 'blog',
            'key'         => 'blog',
            'name'        => 'Latest Insights & Blog Bento',
            'title'       => 'Latest Insights & Blog Bento',
            'description' => 'Recent articles, tech trends, thought leadership, and news cards.',
        ],
        'faq' => [
            'id'          => 'faq',
            'key'         => 'faq',
            'name'        => 'FAQ & Accordion Questions',
            'title'       => 'FAQ & Accordion Questions',
            'description' => 'Frequently asked questions with expandable answer drawers.',
        ],
        'cta' => [
            'id'          => 'cta',
            'key'         => 'cta',
            'name'        => 'Call-to-Action & Contact Banner',
            'title'       => 'Call-to-Action & Contact Banner',
            'description' => 'High-conversion banner inviting users to schedule consultations or contact sales.',
        ],
    ];

    /**
     * Default visibility map (all true).
     */
    public static function defaults(): array
    {
        $map = [];
        foreach (static::$validSections as $sec) {
            $map[$sec] = true;
        }
        return $map;
    }

    public static function isValidSection(string $sectionId): bool
    {
        return in_array($sectionId, static::$validSections, true);
    }

    /**
     * Get or initialize the singleton record.
     */
    public static function getSingleton(): static
    {
        $record = static::first();
        if (! $record) {
            $record = static::create([
                'sections' => static::defaults(),
            ]);
        }
        return $record;
    }

    /**
     * Return normalized sections map with all 10 keys present as booleans.
     */
    public function getMap(): array
    {
        $current = is_array($this->sections) ? $this->sections : [];
        $defaults = static::defaults();

        $map = [];
        foreach (static::$validSections as $sec) {
            $map[$sec] = isset($current[$sec]) ? (bool) $current[$sec] : $defaults[$sec];
        }

        return $map;
    }

    /**
     * Return detailed list of sections with title, description, and status for UI cards.
     */
    public function getDetailedList(): array
    {
        $map = $this->getMap();
        $list = [];

        foreach (static::$sectionMetadata as $id => $meta) {
            $enabled = $map[$id] ?? true;
            $list[] = array_merge($meta, [
                'enabled' => (bool) $enabled,
                'visible' => (bool) $enabled,
                'status'  => $enabled ? 'visible' : 'hidden',
            ]);
        }

        return $list;
    }
}

