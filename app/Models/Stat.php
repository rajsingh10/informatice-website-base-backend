<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    protected $table = 'stats';

    protected $fillable = [
        'number',
        'suffix',
        'label',
        'icon',
        'description',
        'isFeatured',
        'order',
    ];

    protected $casts = [
        'number'     => 'float',
        'isFeatured' => 'boolean',
        'order'      => 'integer',
    ];

    /** Default initial seed dataset matching user spec */
    public static array $defaultStats = [
        [
            'number'      => 250,
            'suffix'      => '+',
            'label'       => 'Projects Completed',
            'icon'        => 'Briefcase',
            'description' => 'Successfully delivered enterprise projects',
            'isFeatured'  => true,
            'order'       => 1,
        ],
        [
            'number'      => 150,
            'suffix'      => '+',
            'label'       => 'Happy Clients',
            'icon'        => 'Users',
            'description' => 'Satisfied partners across industries',
            'isFeatured'  => true,
            'order'       => 2,
        ],
        [
            'number'      => 12,
            'suffix'      => '+',
            'label'       => 'Years Experience',
            'icon'        => 'Award',
            'description' => 'Proven track record of excellence',
            'isFeatured'  => true,
            'order'       => 3,
        ],
        [
            'number'      => 99,
            'suffix'      => '%',
            'label'       => 'Client Satisfaction',
            'icon'        => 'ThumbsUp',
            'description' => 'Positive feedback and retention',
            'isFeatured'  => true,
            'order'       => 4,
        ],
    ];

    /**
     * Auto-seed defaults if table is empty.
     */
    public static function seedDefaultsIfEmpty(): void
    {
        if (static::count() === 0) {
            foreach (self::$defaultStats as $st) {
                static::create($st);
            }
        }
    }

    /**
     * Format payload matching specification exactly.
     */
    public function toPayload(): array
    {
        $num = (float) $this->number;
        $formattedNum = (floor($num) == $num) ? (int) $num : $num;

        return [
            'id'          => (int) $this->id,
            'number'      => $formattedNum,
            'suffix'      => (string) ($this->suffix ?? ''),
            'label'       => (string) $this->label,
            'icon'        => (string) ($this->icon ?? 'Briefcase'),
            'description' => (string) ($this->description ?? ''),
            'isFeatured'  => (bool) $this->isFeatured,
            'order'       => (int) $this->order,
            // Aliases for compatibility
            'visible'     => (bool) $this->isFeatured,
            'title'       => (string) $this->label,
            'value'       => $formattedNum,
        ];
    }
}

