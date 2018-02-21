<?php

namespace App\Filters;

use App\News;
use Illuminate\Http\Request;

class TargetSalesmanFilters extends QueryFilters
{

    /**
     * Ordering data by target
     */
    public function targetsalesman($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('user_id', $value) : null;
    }


}