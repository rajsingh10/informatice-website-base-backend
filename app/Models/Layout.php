<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layout extends Model
{
    /**
     * The primary key for the model.
     * We use the string 'key' column as the PK.
     */
    protected $primaryKey = 'key';

    /**
     * The PK is a string, not an auto-incrementing integer.
     */
    public $incrementing = false;
    protected $keyType   = 'string';

    /**
     * We manage updatedAt manually; disable created_at.
     */
    public $timestamps = false;

    protected $fillable = ['key', 'templateId', 'updatedAt'];

    // ──────────────────────────────────────────────────────────────────────
    // Allowed templates per layout key.
    // Add more values here as new templates are built.
    // ──────────────────────────────────────────────────────────────────────
    public static array $allowedTemplates = [
        'topbarLayout'   => ['layout1', 'layout2', 'layout3'],
        'navbarLayout'   => ['layout1', 'layout2', 'layout3'],
        'homePageLayout' => ['layout1', 'layout2', 'layout3', 'layout4'],
        'footerLayout'   => ['layout1', 'layout2', 'layout3'],
    ];

    /**
     * Returns all valid layout keys (combining defaults and any custom ones in DB).
     */
    public static function validKeys(): array
    {
        try {
            $dbKeys = static::pluck('key')->toArray();
        } catch (\Throwable $e) {
            $dbKeys = [];
        }
        $defaultKeys = array_keys(static::$allowedTemplates);
        return array_values(array_unique(array_merge($defaultKeys, $dbKeys)));
    }

    /**
     * Returns allowed template IDs for a given key.
     */
    public static function allowedFor(string $key): array
    {
        return static::$allowedTemplates[$key] ?? [];
    }
}

