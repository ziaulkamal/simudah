<?php

namespace App\Models;

use App\Models\People;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeopleDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'people_id',
        'document_type',
        'file_path',
        'status',
    ];

    public function people()
    {
        return $this->belongsTo(People::class);
    }
}
