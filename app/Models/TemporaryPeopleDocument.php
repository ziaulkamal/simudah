<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\SecureFileService;

class TemporaryPeopleDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'temporary_people_id',
        'type',
        'original_name',
        'mime_type',
        'encrypted_path',
    ];

    public function getDecryptedFile()
    {
        return SecureFileService::getDecryptedFile($this->encrypted_path);
    }

    public function download()
    {
        $content = $this->getDecryptedFile();
        return response($content)
            ->header('Content-Type', $this->mime_type)
            ->header('Content-Disposition', 'attachment; filename="' . $this->original_name . '"');
    }
}
