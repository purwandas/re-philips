<?php

namespace App\Filters;

use App\Account;
use Illuminate\Http\Request;

class SubChannelFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%') : null;
    }

}