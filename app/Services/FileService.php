<?php


namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public static function upload($file, string $path): string
    {
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($path, $fileName, 'public');
        return $path;
    }

    public static function delete( $path): void
    {
        Storage::disk('public')->delete( $path);
    }

    public static function replace(UploadedFile $file, string $oldPath, $newPath): string
    {
        $newPath = self::upload($file, $newPath);
        self::delete($oldPath);
        return $newPath;
    }

}
