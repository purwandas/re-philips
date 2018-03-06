<?php

namespace App\Filters;

use App\Product;
use Illuminate\Http\Request;

class ProductFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('products.name', 'like', '%'.$value.'%') : null;
    } 

}