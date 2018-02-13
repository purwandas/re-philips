<?php

namespace App\Filters;

use App\Area;
use Illuminate\Http\Request;

class ClassificationFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('classification', 'like', '%'.$value.'%') : null;
    }

}