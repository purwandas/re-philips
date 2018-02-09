<?php

namespace App\Filters;

use App\Area;
use Illuminate\Http\Request;

class GradingFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('grading', 'like', '%'.$value.'%') : null;
    }

}