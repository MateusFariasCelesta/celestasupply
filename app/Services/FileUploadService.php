<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public function store(UploadedFile $file, string $directory): array
    {
        $path = $file->store($directory, 'local');

        return [
            'original_name' => $file->getClientOriginalName(),
            'path'          => $path,
            'mime_type'     => $file->getMimeType() ?? $file->getClientMimeType(),
            'size_kb'       => (int) ceil($file->getSize() / 1024),
        ];
    }

    public function delete(string $path): void
    {
        Storage::disk('local')->delete($path);
    }
}
