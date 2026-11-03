<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FloatingButton extends Model
{
    protected $table = 'floating_buttons';

    protected $fillable = [
        'enabled',
        'buttonText',
        'buttonLink',
        'buttonColor',
        'textColor',
        'position',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    /** Default button configuration */
    public static function defaults(): array
    {
        return [
            'enabled'        => true,
            'buttonText'     => 'Contact us',
            'buttonLink'     => '/contact',
            'buttonColor'    => '#2563eb',
            'textColor'      => '#ffffff',
            'position'       => 'bottom-right',
            // Aliases for compatibility
            'text'           => 'Contact us',
            'link'           => '/contact',
            'screenPosition' => 'bottom-right',
        ];
    }

    /**
     * Get or create the singleton record.
     */
    public static function getSingleton(): self
    {
        $instance = static::first();

        if (! $instance) {
            $instance = static::create([
                'enabled'     => true,
                'buttonText'  => 'Contact us',
                'buttonLink'  => '/contact',
                'buttonColor' => '#2563eb',
                'textColor'   => '#ffffff',
                'position'    => 'bottom-right',
            ]);
        }

        return $instance;
    }

    /**
     * Format payload for Admin response.
     */
    public function toPayload(): array
    {
        return [
            'id'              => $this->id,
            'enabled'         => (bool) $this->enabled,
            'buttonText'      => $this->buttonText ?: 'Contact us',
            'buttonLink'      => $this->buttonLink ?: '/contact',
            'buttonColor'     => $this->buttonColor ?: '#2563eb',
            'textColor'       => $this->textColor ?: '#ffffff',
            'position'        => $this->position ?: 'bottom-right',
            // Aliases for camelCase & snake_case compatibility
            'text'            => $this->buttonText ?: 'Contact us',
            'link'            => $this->buttonLink ?: '/contact',
            'screenPosition'  => $this->position ?: 'bottom-right',
            'button_text'     => $this->buttonText ?: 'Contact us',
            'button_link'     => $this->buttonLink ?: '/contact',
            'button_color'    => $this->buttonColor ?: '#2563eb',
            'text_color'      => $this->textColor ?: '#ffffff',
            'screen_position' => $this->position ?: 'bottom-right',
            'updatedAt'       => $this->updated_at?->toISOString() ?? now()->toISOString(),
        ];
    }

    /**
     * Format payload for Public website response.
     */
    public function toPublicPayload(): array
    {
        return [
            'enabled'        => (bool) $this->enabled,
            'buttonText'     => $this->buttonText ?: 'Contact us',
            'buttonLink'     => $this->buttonLink ?: '/contact',
            'buttonColor'    => $this->buttonColor ?: '#2563eb',
            'textColor'      => $this->textColor ?: '#ffffff',
            'position'       => $this->position ?: 'bottom-right',
            'text'           => $this->buttonText ?: 'Contact us',
            'link'           => $this->buttonLink ?: '/contact',
            'screenPosition' => $this->position ?: 'bottom-right',
            'updatedAt'      => $this->updated_at?->toISOString() ?? now()->toISOString(),
        ];
    }
}

