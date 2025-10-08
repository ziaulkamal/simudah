<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class People extends Model
{
    use HasFactory;

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

    public function setIdentityNumberAttribute($value)
    {
        if ($value) {
            $this->attributes['identityNumber'] = Crypt::encryptString($value);
            $this->attributes['identity_hash'] = hash('sha256', $value);
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
            $this->attributes['family_identity_hash'] = hash('sha256', $value);
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
}
