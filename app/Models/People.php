<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Kemendagri\Districts;
use App\Models\Kemendagri\Provinces;
use App\Models\Kemendagri\Regencies;
use App\Models\Kemendagri\Villages;
use App\Models\PeopleDocument;
use App\Models\Role;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class People extends Model
{
    use HasFactory;

    protected $table = 'peoples';

    protected $secret; // HMAC secret

    protected $fillable = [
        'fullName',
        'age',
        'birthdate',
        'identityNumber',
        'familyIdentityNumber',
        'gender',
        'streetAddress',
        'religion',
        'provinceId',
        'regencieId',
        'districtId',
        'villageId',
        'phoneNumber',
        'email',
        'latitude',
        'longitude',
        'role_id',
        'category_id',
    ];

    /**
     * Booted: inisialisasi secret HMAC
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->secret = env('APP_KEY');
        });

        static::retrieved(function ($model) {
            $model->secret = env('APP_KEY');
        });
    }

    /**
     * Ambil secret HMAC
     */
    public function getSecret(): string
    {
        return $this->secret ?? env('APP_KEY');
    }

    /**
     * Generate HMAC untuk NIK / KK
     */
    public static function generateHmac(string $value): string
    {
        $secret = env('APP_KEY');
        // $value = preg_replace('/\D/', '', $value); // pastikan hanya angka
        return hash_hmac('sha256', $value, $secret);
    }

    // --- Mutators ---
    public function setIdentityNumberAttribute($value)
    {
        if ($value) {
            $this->attributes['identityNumber'] = Crypt::encryptString($value);
            $this->attributes['identity_hash'] = $this->generateHmac($value);
        }
    }

    public function getIdentityNumberAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setFamilyIdentityNumberAttribute($value)
    {
        if ($value) {
            $this->attributes['familyIdentityNumber'] = Crypt::encryptString($value);
            $this->attributes['family_identity_hash'] = $this->generateHmac($value);
        }
    }

    public function getFamilyIdentityNumberAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    // --- RELATIONSHIPS ---
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function documents()
    {
        return $this->hasMany(PeopleDocument::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function province()
    {
        return $this->belongsTo(Provinces::class, 'provinceId');
    }

    public function regencie()
    {
        return $this->belongsTo(Regencies::class, 'regencieId');
    }

    public function district()
    {
        return $this->belongsTo(Districts::class, 'districtId');
    }

    public function village()
    {
        return $this->belongsTo(Villages::class, 'villageId');
    }
}
