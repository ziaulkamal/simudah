<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class TemporaryPeople extends Model
{
    use HasFactory;

    protected $table = 'temporary_peoples';

    protected $fillable = [
        'fullName',
        'identityNumber',
        'phoneNumber',
        'birthdate',
        'gender',
        'is_verified',
        'otp_code',
        'otp_expires_at',
        // 'secret',
    ];

    protected $hidden = ['identityNumber', 'otp_code'];

    // protected static function booted()
    // {
    //     static::creating(fn($m) => $m->secret = env('APP_KEY'));
    //     static::retrieved(fn($m) => $m->secret = env('APP_KEY'));
    // }

    public function getSecret(): string
    {
        return $this->secret ?? env('APP_KEY');
    }

    public static function generateHmac(string $value): string
    {
        return hash_hmac('sha256', $value, env('APP_KEY'));
    }

    // Mutator NIK
    public function setIdentityNumberAttribute($value)
    {
        if ($value) {
            $this->attributes['identityNumber'] = Crypt::encryptString($value);
            $this->attributes['identity_hash'] = static::generateHmac($value);
        }
    }

    public function getIdentityNumberAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function documents()
    {
        return $this->hasMany(TemporaryPeopleDocument::class);
    }
}
