<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeopleCategoryHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'people_id',
        'category_id',
    ];

    public function people()
    {
        return $this->belongsTo(People::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
