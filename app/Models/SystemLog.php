<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'people_id',
        'type',
        'title',
        'message',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function people()
    {
        return $this->belongsTo(People::class);
    }
}
