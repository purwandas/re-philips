<?php

namespace App\Filters;

use App\User;
use Illuminate\Http\Request;

class UserFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%') : null;
    } 

    /* Ordering data by role */
    public function role($value){
    	return $this->builder->where('role', $value);
    }

    /* Ordering data by role group */
    public function roleGroup($value){
        return $this->builder->whereIn('role', $value);
    }

}