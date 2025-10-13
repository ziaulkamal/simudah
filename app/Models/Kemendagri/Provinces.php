<?php

namespace App\Models\Kemendagri;

use App\Models\Kemendagri\Regencies;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provinces extends Model
{
    protected $table = 'reg_provinces';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    public function regencies()
    {
        return $this->hasMany(Regencies::class, 'province_id', 'id');
    }
}
