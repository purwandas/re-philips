<?php

namespace App\Filters;

use App\News;
use Illuminate\Http\Request;

class PriceFilters extends QueryFilters
{

    /**
     * Ordering data by price
     */
    public function price($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('price', $value) : null;
    }

}