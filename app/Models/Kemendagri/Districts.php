<?php

namespace App\Models\Kemendagri;

use App\Models\Kemendagri\Regencies;
use App\Models\Kemendagri\Villages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Districts extends Model
{
    protected $table = 'reg_districts';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    public function regency()
    {
        return $this->belongsTo(Regencies::class, 'regency_id', 'id');
    }

    public function villages()
    {
        return $this->hasMany(Villages::class, 'district_id', 'id');
    }

}
