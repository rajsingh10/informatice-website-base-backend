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
}

