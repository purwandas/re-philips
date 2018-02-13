<?php

namespace App\Filters;

use App\Posm;
use Illuminate\Http\Request;

class PosmFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%') : null;
    }

}