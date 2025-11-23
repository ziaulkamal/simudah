<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\People;
use App\Models\Role;

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

    protected $appends = ['address', 'fullName', 'phoneNumber'];

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
        static::creating(function ($trx) {
            if (empty($trx->transaction_code)) {
                $trx->transaction_code = 'TRX-' . strtoupper(uniqid());
            }
        });
    }

    public static function getFullTransactionByCode(string $code)
    {
        return self::with([
            'people.role',
            'people.category',
            'people.documents',
            'people.location',
            'people.province',
            'people.regencie',
            'people.district',
            'people.village',
            'category',
            'role'
        ])->where('transaction_code', $code)->first();
    }

    public function getAddressAttribute()
    {
        return [
            'street'   => $this->people->location->street ?? '-',
            'province' => $this->people->province->name ?? '-',
            'regencie' => $this->people->regencie->name ?? '-',
            'district' => $this->people->district->name ?? '-',
            'village'  => $this->people->village->name ?? '-',
        ];
    }

    public function getFullNameAttribute()
    {
        return $this->people->fullName ?? '-';
    }

    public function getPhoneNumberAttribute()
    {
        return $this->people->phoneNumber ?? '-';
    }
}
