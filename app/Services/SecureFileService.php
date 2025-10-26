<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SecureFileService
{
    public static function storeEncryptedFile($file, $pathPrefix = 'encrypted_temp/')
    {
        $filename = Str::random(40) . '.enc';
        $storagePath = $pathPrefix . $filename;

        $contents = file_get_contents($file->getRealPath());
        $encrypted = Crypt::encrypt($contents);

        Storage::put($storagePath, $encrypted);

        return $storagePath;
    }

    public static function getDecryptedFile($encryptedPath)
    {
        $encrypted = Storage::get($encryptedPath);
        return Crypt::decrypt($encrypted);
    }
}
