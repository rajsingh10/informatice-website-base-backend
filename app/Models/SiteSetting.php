<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $table = 'site_settings';

    protected $fillable = [
        'siteName',
        'logoUrl',
        'faviconUrl',
        'email',
        'phone',
        'address',
        'mapEmbedUrl',
        'offices',
        'socials',
        'announcement',
        // Grouped JSON configs
        'spacing',
        'textDesign',
        'buttonStyle',
        'advanced',
        'branding',
        // Flat individual parameters
        // 1. Spacing
        'outerMargin',
        'paddingX',
        'paddingY',
        'borderRadius',
        'cardRadius',
        // 2. Text design
        'fontFamilyHeading',
        'fontFamilyBody',
        'fontSizeBase',
        'fontWeightHeading',
        'fontWeightBody',
        // 3. Button style
        'buttonRadius',
        'buttonPaddingX',
        'buttonPaddingY',
        'buttonShadow',
        'buttonHoverAnimation',
        // 4. Advanced
        'customHeaderCode',
        'customFooterCode',
        'googleAnalyticsId',
        // 5. Branding
        'primaryColor',
        'secondaryColor',
        'accentColor',
        'textColor',
        'backgroundColor',
        // Header / Logo alignment
        'logoPosition',
        'logoAlignment',
    ];

    protected $casts = [
        'offices'      => 'array',
        'socials'      => 'array',
        'announcement' => 'array',
        'spacing'      => 'array',
        'textDesign'   => 'array',
        'buttonStyle'  => 'array',
        'advanced'     => 'array',
        'branding'     => 'array',
    ];

    /**
     * Default settings payload.
     */
    public static function defaults(): array
    {
        $spacing = [
            'outerMargin'  => '0 auto',
            'paddingX'     => '1.5rem',
            'paddingY'     => '2rem',
            'borderRadius' => '0.5rem',
            'cardRadius'   => '0.75rem',
        ];

        $textDesign = [
            'fontFamilyHeading' => 'Inter, sans-serif',
            'fontFamilyBody'    => 'Inter, sans-serif',
            'fontSizeBase'      => '16px',
            'fontWeightHeading' => '700',
            'fontWeightBody'    => '400',
        ];

        $buttonStyle = [
            'buttonRadius'         => '0.5rem',
            'buttonPaddingX'        => '1.25rem',
            'buttonPaddingY'        => '0.625rem',
            'buttonShadow'         => '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
            'buttonHoverAnimation' => 'translate-y',
        ];

        $advanced = [
            'customHeaderCode'  => '',
            'customFooterCode'  => '',
            'googleAnalyticsId' => '',
        ];

        $branding = [
            'primaryColor'    => '#2563eb',
            'secondaryColor'  => '#1e40af',
            'accentColor'     => '#38bdf8',
            'textColor'       => '#1e293b',
            'backgroundColor' => '#ffffff',
        ];

        return [
            'siteName'     => 'Acme',
            'logoUrl'      => '',
            'faviconUrl'   => '',
            'email'        => '',
            'phone'        => '',
            'address'      => '',
            'mapEmbedUrl'  => '',
            'logoPosition' => 'left',
            'logoAlignment' => 'left',
            'offices'      => [
                [
                    'name'    => 'HQ',
                    'address' => '',
                    'phone'   => '',
                    'email'   => '',
                ],
            ],
            'socials'      => [
                'facebook'  => '',
                'instagram' => '',
                'linkedin'  => '',
                'twitter'   => '',
                'youtube'   => '',
            ],
            'announcement' => [
                'enabled' => false,
                'text'    => '',
                'url'     => '',
            ],
            'spacing'      => $spacing,
            'textDesign'   => $textDesign,
            'buttonStyle'  => $buttonStyle,
            'advanced'     => $advanced,
            'branding'     => $branding,
            // Spacing
            'outerMargin'  => $spacing['outerMargin'],
            'paddingX'     => $spacing['paddingX'],
            'paddingY'     => $spacing['paddingY'],
            'borderRadius' => $spacing['borderRadius'],
            'cardRadius'   => $spacing['cardRadius'],
            // Text design
            'fontFamilyHeading' => $textDesign['fontFamilyHeading'],
            'fontFamilyBody'    => $textDesign['fontFamilyBody'],
            'fontSizeBase'      => $textDesign['fontSizeBase'],
            'fontWeightHeading' => $textDesign['fontWeightHeading'],
            'fontWeightBody'    => $textDesign['fontWeightBody'],
            // Button style
            'buttonRadius'         => $buttonStyle['buttonRadius'],
            'buttonPaddingX'        => $buttonStyle['buttonPaddingX'],
            'buttonPaddingY'        => $buttonStyle['buttonPaddingY'],
            'buttonShadow'         => $buttonStyle['buttonShadow'],
            'buttonHoverAnimation' => $buttonStyle['buttonHoverAnimation'],
            // Advanced
            'customHeaderCode'  => $advanced['customHeaderCode'],
            'customFooterCode'  => $advanced['customFooterCode'],
            'googleAnalyticsId' => $advanced['googleAnalyticsId'],
            // Branding
            'primaryColor'    => $branding['primaryColor'],
            'secondaryColor'  => $branding['secondaryColor'],
            'accentColor'     => $branding['accentColor'],
            'textColor'       => $branding['textColor'],
            'backgroundColor' => $branding['backgroundColor'],
        ];
    }

    /**
     * Get or create the singleton settings record.
     */
    public static function getSingleton(): static
    {
        $setting = static::first();
        if (! $setting) {
            $setting = static::create(static::defaults());
        }
        return $setting;
    }

    /**
     * Format payload.
     */
    public function toPayload(): array
    {
        $defaults = static::defaults();

        // Resolve grouped structures
        $storedSpacing = is_array($this->spacing) ? $this->spacing : [];
        $spacing = [
            'outerMargin'  => (string) ($this->outerMargin ?? ($storedSpacing['outerMargin'] ?? $defaults['outerMargin'])),
            'paddingX'     => (string) ($this->paddingX ?? ($storedSpacing['paddingX'] ?? $defaults['paddingX'])),
            'paddingY'     => (string) ($this->paddingY ?? ($storedSpacing['paddingY'] ?? $defaults['paddingY'])),
            'borderRadius' => (string) ($this->borderRadius ?? ($storedSpacing['borderRadius'] ?? $defaults['borderRadius'])),
            'cardRadius'   => (string) ($this->cardRadius ?? ($storedSpacing['cardRadius'] ?? $defaults['cardRadius'])),
        ];

        $storedText = is_array($this->textDesign) ? $this->textDesign : [];
        $textDesign = [
            'fontFamilyHeading' => (string) ($this->fontFamilyHeading ?? ($storedText['fontFamilyHeading'] ?? $defaults['fontFamilyHeading'])),
            'fontFamilyBody'    => (string) ($this->fontFamilyBody ?? ($storedText['fontFamilyBody'] ?? $defaults['fontFamilyBody'])),
            'fontSizeBase'      => (string) ($this->fontSizeBase ?? ($storedText['fontSizeBase'] ?? $defaults['fontSizeBase'])),
            'fontWeightHeading' => (string) ($this->fontWeightHeading ?? ($storedText['fontWeightHeading'] ?? $defaults['fontWeightHeading'])),
            'fontWeightBody'    => (string) ($this->fontWeightBody ?? ($storedText['fontWeightBody'] ?? $defaults['fontWeightBody'])),
        ];

        $storedButton = is_array($this->buttonStyle) ? $this->buttonStyle : [];
        $buttonStyle = [
            'buttonRadius'         => (string) ($this->buttonRadius ?? ($storedButton['buttonRadius'] ?? $defaults['buttonRadius'])),
            'buttonPaddingX'        => (string) ($this->buttonPaddingX ?? ($storedButton['buttonPaddingX'] ?? $defaults['buttonPaddingX'])),
            'buttonPaddingY'        => (string) ($this->buttonPaddingY ?? ($storedButton['buttonPaddingY'] ?? $defaults['buttonPaddingY'])),
            'buttonShadow'         => (string) ($this->buttonShadow ?? ($storedButton['buttonShadow'] ?? $defaults['buttonShadow'])),
            'buttonHoverAnimation' => (string) ($this->buttonHoverAnimation ?? ($storedButton['buttonHoverAnimation'] ?? $defaults['buttonHoverAnimation'])),
        ];

        $storedAdv = is_array($this->advanced) ? $this->advanced : [];
        $advanced = [
            'customHeaderCode'  => (string) ($this->customHeaderCode ?? ($storedAdv['customHeaderCode'] ?? $defaults['customHeaderCode'])),
            'customFooterCode'  => (string) ($this->customFooterCode ?? ($storedAdv['customFooterCode'] ?? $defaults['customFooterCode'])),
            'googleAnalyticsId' => (string) ($this->googleAnalyticsId ?? ($storedAdv['googleAnalyticsId'] ?? $defaults['googleAnalyticsId'])),
        ];

        $storedBrand = is_array($this->branding) ? $this->branding : [];
        $branding = [
            'primaryColor'    => (string) ($this->primaryColor ?? ($storedBrand['primaryColor'] ?? $defaults['primaryColor'])),
            'secondaryColor'  => (string) ($this->secondaryColor ?? ($storedBrand['secondaryColor'] ?? $defaults['secondaryColor'])),
            'accentColor'     => (string) ($this->accentColor ?? ($storedBrand['accentColor'] ?? $defaults['accentColor'])),
            'textColor'       => (string) ($this->textColor ?? ($storedBrand['textColor'] ?? $defaults['textColor'])),
            'backgroundColor' => (string) ($this->backgroundColor ?? ($storedBrand['backgroundColor'] ?? $defaults['backgroundColor'])),
        ];

        return [
            'siteName'     => (string) ($this->siteName ?? $defaults['siteName']),
            'logoUrl'      => (string) ($this->logoUrl ?? ''),
            'faviconUrl'   => (string) ($this->faviconUrl ?? ''),
            'email'        => (string) ($this->email ?? ''),
            'phone'        => (string) ($this->phone ?? ''),
            'address'      => (string) ($this->address ?? ''),
            'mapEmbedUrl'  => (string) ($this->mapEmbedUrl ?? ''),
            'logoPosition' => 'left',
            'logoAlignment' => 'left',
            'offices'      => is_array($this->offices) ? $this->offices : $defaults['offices'],
            'socials'      => is_array($this->socials) ? array_merge($defaults['socials'], $this->socials) : $defaults['socials'],
            'announcement' => is_array($this->announcement) ? array_merge($defaults['announcement'], $this->announcement) : $defaults['announcement'],
            // Grouped objects
            'spacing'      => $spacing,
            'textDesign'   => $textDesign,
            'buttonStyle'  => $buttonStyle,
            'advanced'     => $advanced,
            'branding'     => $branding,
            // Flat individual properties for direct access
            'outerMargin'  => $spacing['outerMargin'],
            'paddingX'     => $spacing['paddingX'],
            'paddingY'     => $spacing['paddingY'],
            'borderRadius' => $spacing['borderRadius'],
            'cardRadius'   => $spacing['cardRadius'],
            'fontFamilyHeading' => $textDesign['fontFamilyHeading'],
            'fontFamilyBody'    => $textDesign['fontFamilyBody'],
            'fontSizeBase'      => $textDesign['fontSizeBase'],
            'fontWeightHeading' => $textDesign['fontWeightHeading'],
            'fontWeightBody'    => $textDesign['fontWeightBody'],
            'buttonRadius'         => $buttonStyle['buttonRadius'],
            'buttonPaddingX'        => $buttonStyle['buttonPaddingX'],
            'buttonPaddingY'        => $buttonStyle['buttonPaddingY'],
            'buttonShadow'         => $buttonStyle['buttonShadow'],
            'buttonHoverAnimation' => $buttonStyle['buttonHoverAnimation'],
            'customHeaderCode'  => $advanced['customHeaderCode'],
            'customFooterCode'  => $advanced['customFooterCode'],
            'googleAnalyticsId' => $advanced['googleAnalyticsId'],
            'primaryColor'    => $branding['primaryColor'],
            'secondaryColor'  => $branding['secondaryColor'],
            'accentColor'     => $branding['accentColor'],
            'textColor'       => $branding['textColor'],
            'backgroundColor' => $branding['backgroundColor'],
        ];
    }

    /**
     * [Contact Page API] - Format contact info payload.
     */
    public function toContactInfo(): array
    {
        $defaults = static::defaults();

        return [
            'email'       => (string) ($this->email ?? ''),
            'phone'       => (string) ($this->phone ?? ''),
            'address'     => (string) ($this->address ?? ''),
            'mapEmbedUrl' => (string) ($this->mapEmbedUrl ?? ''),
            'offices'     => is_array($this->offices) ? $this->offices : $defaults['offices'],
            'socials'     => is_array($this->socials) ? array_merge($defaults['socials'], $this->socials) : $defaults['socials'],
        ];
    }
}

