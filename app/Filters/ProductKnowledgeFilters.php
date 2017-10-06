<?php

namespace App\Filters;

use App\News;
use Illuminate\Http\Request;

class ProductKnowledgeFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%') : null;
    }

}