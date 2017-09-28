<?php

namespace App\Filters;

use App\GroupCompetitor;
use Illuminate\Http\Request;

class GroupCompetitorFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%') : null;
    } 

}