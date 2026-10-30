<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layout extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = ['key', 'templateId', 'updatedAt'];

    public static array $modules = [
        'topbarLayout' => [
            'name' => 'Topbar',
            'min' => 0,
            'max' => 4, // layout0 (hidden) to layout4
        ],
        'navbarLayout' => [
            'name' => 'Navbar',
            'min' => 1,
            'max' => 5,
        ],
        'homePageLayout' => [
            'name' => 'Home',
            'min' => 1,
            'max' => 10,
        ],
        'aboutPageLayout' => [
            'name' => 'About',
            'min' => 1,
            'max' => 5,
            'templates' => [
                'layout1' => 'Classic Story & Team Focus',
                'layout2' => 'Modern Timeline & Mission',
                'layout3' => 'Bento Grid with Core Values',
                'layout4' => 'Split Visual Editorial',
                'layout5' => 'Minimal Corporate Overview',
            ],
        ],
        'servicesPageLayout' => [
            'name' => 'Services catalog',
            'min' => 1,
            'max' => 8,
        ],
        'serviceDetailPageLayout' => [
            'name' => 'Service detail',
            'min' => 1,
            'max' => 4,
        ],
        'portfolioPageLayout' => [
            'name' => 'Portfolio catalog',
            'min' => 1,
            'max' => 8,
        ],
        'portfolioDetailPageLayout' => [
            'name' => 'Case study detail',
            'min' => 1,
            'max' => 4,
        ],
        'blogPageLayout' => [
            'name' => 'Blog catalog',
            'min' => 1,
            'max' => 4,
        ],
        'blogDetailPageLayout' => [
            'name' => 'Blog article',
            'min' => 1,
            'max' => 3,
        ],
        'eventsPageLayout' => [
            'name' => 'Events feed',
            'min' => 1,
            'max' => 3,
        ],
        'eventDetailPageLayout' => [
            'name' => 'Event detail',
            'min' => 1,
            'max' => 4,
        ],
        'teamPageLayout' => [
            'name' => 'Team',
            'min' => 1,
            'max' => 5,
        ],
        'testimonialsPageLayout' => [
            'name' => 'Testimonials',
            'min' => 1,
            'max' => 5,
        ],
        'galleryPageLayout' => [
            'name' => 'Gallery',
            'min' => 1,
            'max' => 4,
        ],
        // [Contact Page API] - Module 11
        'contactPageLayout' => [
            'name' => 'Contact',
            'min' => 1,
            'max' => 4,
            'templates' => [
                'layout1' => 'Split Form with Google Map',
                'layout2' => 'Modern Card with Social Hub',
                'layout3' => 'Corporate Multi-office',
                'layout4' => 'Minimal Quick Reach',
            ],
        ],
        'careerPageLayout' => [
            'name' => 'Career',
            'min' => 1,
            'max' => 4,
        ],
        'faqPageLayout' => [
            'name' => 'FAQ',
            'min' => 1,
            'max' => 4,
        ],
        'termsPageLayout' => [
            'name' => 'Terms',
            'min' => 1,
            'max' => 5,
        ],
        'termsConditionsPageLayout' => [
            'name' => 'Terms & Conditions',
            'min' => 1,
            'max' => 5,
        ],
        'privacyPageLayout' => [
            'name' => 'Privacy',
            'min' => 1,
            'max' => 5,
        ],
        'footerLayout' => [
            'name' => 'Footer',
            'min'  => 1,
            'max'  => 4,
        ],
    ];

    /**
     * Map aliases to canonical layout keys.
     */
    public static function canonicalKey(string $key): string
    {
        return match ($key) {
            'termsConditionsPageLayout', 'termsConditionsLayout' => 'termsPageLayout',
            default => $key,
        };
    }

    /**
     * All recognized keys from the specification table.
     */
    public static function validKeys(): array
    {
        return array_keys(static::$modules);
    }

    /**
     * Returns allowed template IDs for a given key (e.g. layout0..layout4 or layout1..layoutN).
     */
    public static function allowedFor(string $key): array
    {
        if (! isset(static::$modules[$key])) {
            return [];
        }

        $min = static::$modules[$key]['min'];
        $max = static::$modules[$key]['max'];

        return array_map(fn ($n) => "layout{$n}", range($min, $max));
    }

    /**
     * Returns module display name for a key (e.g. 'topbarLayout' => 'Topbar').
     */
    public static function moduleName(string $key): string
    {
        return static::$modules[$key]['name'] ?? ucfirst(preg_replace('/Layout$/i', '', $key));
    }

    /**
     * Returns detailed templates list with IDs and human-readable names for a given key.
     */
    public static function templatesFor(string $key): array
    {
        if (isset(static::$modules[$key]['templates'])) {
            return static::$modules[$key]['templates'];
        }

        $allowed = static::allowedFor($key);
        $result = [];
        foreach ($allowed as $id) {
            $result[$id] = 'Template '.strtoupper($id);
        }

        return $result;
    }
}
