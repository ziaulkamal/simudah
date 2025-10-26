<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpLog extends Model
{
    protected $fillable = [
        'phone',
        'otp',
        'status',
        'message',
        'ip',
    ];
}