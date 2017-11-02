<?php

namespace App\Filters;

use App\News;
use Illuminate\Http\Request;

class TargetFilters extends QueryFilters
{

    /**
     * Ordering data by target
     */
    public function target($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', $value) : null;
    }

}