<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\People;
use App\Models\Role;
use App\Models\SystemLog;

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
        'paid_at',
        'due_date',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'due_date' => 'datetime',
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

    protected static function booted()
    {
        // generate kode transaksi unik
        static::creating(function ($trx) {
            if (empty($trx->transaction_code)) {
                $trx->transaction_code = 'TRX-' . strtoupper(uniqid());
            }
        });


    }
}
