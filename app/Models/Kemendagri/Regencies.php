<?php

namespace App\Models\Kemendagri;


use App\Models\Kemendagri\Districts;
use App\Models\Kemendagri\Provinces;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regencies extends Model
{
    protected $table = 'reg_regencies';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    public function province()
    {
        return $this->belongsTo(Provinces::class, 'province_id', 'id');
    }

    public function districts()
    {
        return $this->hasMany(Districts::class, 'regency_id', 'id');
    }


}
