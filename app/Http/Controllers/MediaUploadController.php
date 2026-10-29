<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\AuthController;
use App\Models\MediaUpload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaUploadController extends Controller
{
    /**
     * POST /admin/uploads — Auth Required
     * Upload one image (multipart/form-data, field: file).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:5120', // 5 MB max
                'mimetypes:image/jpeg,image/png,image/webp,image/svg+xml,image/gif',
            ],
        ], [
            'file.required'  => 'Please upload a file.',
            'file.max'       => 'File size cannot exceed 5 MB.',
            'file.mimetypes' => 'File must be an image of type: JPEG, PNG, WebP, SVG, or GIF.',
        ]);

        $file = $request->file('file');

        // Generate clean server-side ID & filename
        $id = MediaUpload::generateId(); // e.g. "f_91ab"
        $extension = $file->guessExtension() ?: $file->getClientOriginalExtension() ?: 'webp';
        $filename = "{$id}.{$extension}";

        // Subfolder path by year/month directly inside public folder (no storage link needed)
        $subDir = 'uploads/' . date('Y/m');
        $destDir = public_path($subDir);
        if (! file_exists($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $mimeType = $file->getMimeType() ?: $file->getClientMimeType() ?: 'application/octet-stream';
        $size = $file->getSize() ?: 0;

        $fullFilePath = $destDir . '/' . $filename;
        $file->move($destDir, $filename);
        $path = $subDir . '/' . $filename;
        $url = AuthController::publicFileUrl($path);

        // Detect dimensions for raster images
        $width = null;
        $height = null;
        if (file_exists($fullFilePath)) {
            $imageInfo = @getimagesize($fullFilePath);
            if ($imageInfo && isset($imageInfo[0], $imageInfo[1])) {
                $width = (int) $imageInfo[0];
                $height = (int) $imageInfo[1];
            }
        }

        $media = MediaUpload::create([
            'id'        => $id,
            'filename'  => $filename,
            'path'      => $path,
            'url'       => $url,
            'mime_type' => $mimeType,
            'size'      => $size,
            'width'     => $width,
            'height'    => $height,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $media->toResponse(),
        ], 201);
    }

    /**
     * DELETE /admin/uploads/:id — Auth Required
     * Remove an uploaded file.
     */
    public function destroy(string $id): JsonResponse
    {
        $media = MediaUpload::find($id);

        if (! $media) {
            return response()->json([
                'success' => false,
                'message' => "Media file '{$id}' not found.",
            ], 404);
        }

        $media->delete(); // Automatically removes file from disk via model event

        return response()->json([
            'success' => true,
            'message' => 'File removed successfully.',
        ], 200);
    }
}

