<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MediaUpload extends Model
{
    protected $table = 'media_uploads';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'filename',
        'path',
        'url',
        'mime_type',
        'size',
        'width',
        'height',
    ];

    protected $casts = [
        'size'   => 'integer',
        'width'  => 'integer',
        'height' => 'integer',
    ];

    /**
     * Generate unique ID like "f_91ab"
     */
    public static function generateId(): string
    {
        do {
            $id = 'f_' . substr(bin2hex(random_bytes(4)), 0, 4);
        } while (static::where('id', $id)->exists());

        return $id;
    }

    /**
     * Delete underlying file from disk when model is deleted.
     */
    protected static function booted()
    {
        static::deleting(function (MediaUpload $media) {
            if ($media->path && Storage::disk('public')->exists($media->path)) {
                Storage::disk('public')->delete($media->path);
            }
        });
    }

    public function toResponse(): array
    {
        return [
            'id'     => $this->id,
            'url'    => $this->url,
            'width'  => $this->width,
            'height' => $this->height,
            'size'   => $this->size,
        ];
    }
}

