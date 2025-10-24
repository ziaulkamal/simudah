<?php

namespace App\Models;

use App\Models\People;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeopleLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'people_id',
        'latitude',
        'longitude',
        'address',
        'province_name',
        'regency_name',
        'district_name',
        'village_name',
    ];

    public function people()
    {
        return $this->belongsTo(People::class, 'people_id', 'id');
    }
}
