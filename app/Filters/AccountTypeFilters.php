<?php

namespace App\Filters;

use App\AccountType;
use Illuminate\Http\Request;

class AccountTypeFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%') : null;
    } 

}