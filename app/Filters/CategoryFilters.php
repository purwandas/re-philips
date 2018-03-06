<?php

namespace App\Filters;

use App\Category;
use Illuminate\Http\Request;

class CategoryFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('categories.name', 'like', '%'.$value.'%') : null;
    } 

}