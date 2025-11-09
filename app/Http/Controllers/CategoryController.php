<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends GlobalController
{
    public function __construct()
    {
        parent::__construct(Category::class);
    }
}
