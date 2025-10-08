<?php

namespace App\Models;

use App\Models\Category;
use App\Models\People;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_code',
        'people_id',
        'category_id',
        'role_id',
        'month',
        'year',
        'amount',
        'status',
        'paid_at'
    ];

    public function people()
    {
        return $this->belongsTo(People::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
