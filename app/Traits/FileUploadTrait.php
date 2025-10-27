<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait FileUploadTrait
{
    /**
     * Upload any file (image, document, etc.)
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder (e.g. 'uploads/products/', 'uploads/logo/')
     * @param string|null $prefix (optional file prefix)
     * @return string
     */
    public function uploadFile($file, $folder = 'uploads/', $prefix = null)
    {
        // Ensure folder exists
        $path = public_path($folder);
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        // Generate unique file name
        $extension = $file->getClientOriginalExtension();
        $filename = ($prefix ?? Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)))
                    . '_' . uniqid() . '.' . $extension;

        // Move file to public folder
        $file->move($path, $filename);

        // Return relative path for database storage
        return $folder . $filename;
    }

    /**
     * Delete a file if exists
     *
     * @param string|null $filePath
     * @return void
     */
    public function deleteFile($filePath)
    {
        if ($filePath && File::exists(public_path($filePath))) {
            File::delete(public_path($filePath));
        }
    }
}
