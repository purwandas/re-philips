<?php

namespace App\Filters;

use App\FeedbackCategory;
use Illuminate\Http\Request;

class FeedbackCategoryFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%') : null;
    } 

}