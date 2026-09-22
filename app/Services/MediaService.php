<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MediaService
{
    public function image(UploadedFile $file): string
    {
        $size = @getimagesize($file->getRealPath());
        if (! $size || $size[0] * $size[1] > 20000000) {
            throw ValidationException::withMessages(['image' => 'Use an image smaller than 20 megapixels.']);
        }
        if (! function_exists('imagecreatefromstring')) {
            throw ValidationException::withMessages(['image' => 'The PHP GD extension is required for image uploads.']);
        }
        $source = @imagecreatefromstring(file_get_contents($file->getRealPath()));
        if (! $source) {
            throw ValidationException::withMessages(['image' => 'The image could not be read.']);
        }
        $width = min(1800, $size[0]);
        $height = max(1, (int) round($size[1] * $width / $size[0]));
        $target = imagecreatetruecolor($width, $height);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        imagecopyresampled($target, $source, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
        ob_start();
        imagewebp($target, null, 85);
        $bytes = ob_get_clean();
        imagedestroy($source);
        imagedestroy($target);
        $path = 'images/'.Str::uuid().'.webp';
        if (! $bytes || ! Storage::disk('public')->put($path, $bytes)) {
            throw ValidationException::withMessages(['image' => 'Image storage failed. Check the storage directory permissions.']);
        }

        return $path;
    }
}
