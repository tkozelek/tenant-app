<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Storage;

class FileService
{
    /**
     * Nahrá súbor do storagu a vytvorí záznam v databáze
     */
    public function uploadFile(UploadedFile $file): File
    {
        $timestamp = now()->format('YmdHis');
        $uniqueName = "{$timestamp}_{$file->getClientOriginalName()}";
        $path = $file->storeAs('uploads', $uniqueName, 'public');

        return File::create([
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);
    }

    /**
     * Získa cestu k súboru pre zobrazenie.
     */
    public function getFilePath(File $file): string
    {
        return Storage::url($file->path);
    }

    /**
     * Vymaže súbor zo systému a záznam z databázy.
     */
    public function deleteFile(File $file): void
    {
        Storage::disk('public')->delete($file->path);
        $file->delete();
    }
}
