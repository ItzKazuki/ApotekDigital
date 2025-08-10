<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

trait StoreBase64Image
{
    /**
     * Stores a base64-encoded image into the specified folder path.
     *
     * This method accepts a base64-encoded image string (in PNG or JPEG format),
     * decodes it, and stores it in the given folder path using Laravel's Storage facade.
     *
     * @param string $folderPath   The folder path inside the storage disk (without trailing slash).
     *                             Example: 'images/products'
     * @param string $base64Image  The base64-encoded image string, including the MIME type prefix.
     *                             Example: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...'
     * @param string $disk         The storage disk to use ('public', 'local', etc.). Default: 'public'.
     *
     * @return string Returns the file path (or URL if stored in a public disk).
     *
     * @throws \Exception If decoding or file storage fails.
     */
    public function storeBase64Image($folderPath, $base64Image, $disk = 'public')
    {
        try {
            // Detect file extension from base64 prefix
            if (str_starts_with($base64Image, 'data:image/png;base64,')) {
                $extension = 'png';
                $imageData = str_replace('data:image/png;base64,', '', $base64Image);
            } elseif (str_starts_with($base64Image, 'data:image/jpeg;base64,') || str_starts_with($base64Image, 'data:image/jpg;base64,')) {
                $extension = 'jpg';
                $imageData = str_replace(['data:image/jpeg;base64,', 'data:image/jpg;base64,'], '', $base64Image);
            } else {
                throw new \Exception('Unsupported image format. Only PNG and JPEG are allowed.');
            }

            // Decode image
            $imageData = str_replace(' ', '+', $imageData);
            $imageData = base64_decode($imageData);

            if ($imageData === false) {
                throw new \Exception('Invalid base64 image data.');
            }

            // Pastikan folder ada
            if (!Storage::disk($disk)->exists($folderPath)) {
                Storage::disk($disk)->makeDirectory($folderPath);
            }

            // Generate unique file name
            $fileName = uniqid('', true) . '.' . $extension;
            $filePath = trim($folderPath, '/\\') . DIRECTORY_SEPARATOR . $fileName;

            // Store image
            Storage::disk($disk)->put($filePath, $imageData);

            return $filePath;
        } catch (\Exception $e) {
            // Log the error (truncate base64 data for security)
            Log::error('Failed to upload base64 image: ' . $e->getMessage(), [
                'filePath' => isset($filePath) ? $filePath : null,
                'base64Image' => substr($base64Image, 0, 50) . '...',
            ]);

            throw $e;
        }
    }
}
