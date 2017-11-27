<?php

namespace App\Filters;

use App\News;
use Illuminate\Http\Request;

class ProductFocusFilters extends QueryFilters
{

    /**
     * Ordering data by type
     */
    public function type($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('type', $value) : null;
    }

}