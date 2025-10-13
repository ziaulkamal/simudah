<?php

namespace App\Models\Kemendagri;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Villages extends Model
{
    protected $table = 'reg_villages';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    public function district()
    {
        return $this->belongsTo(Districts::class, 'district_id', 'id');
    }
}
