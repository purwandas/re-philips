<?php

namespace App\Filters;

use App\AreaApp;
use Illuminate\Http\Request;

class AreaAppFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%') : null;
    } 

}