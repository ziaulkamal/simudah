<?php

namespace App\Models;

use App\Models\People;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'status',
    ];

    public function peoples()
    {
        return $this->hasMany(People::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
