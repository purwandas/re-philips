<?php

namespace App\Filters;

use App\Group;
use Illuminate\Http\Request;

class GroupFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('groups.name', 'like', '%'.$value.'%') : null;
    } 

    // Order by region
    public function byGroupProduct($value) {
        return $this->builder->whereHas('groupproduct', function ($query) use ($value) {
            return $query->where('group_products.id',$value);
        });
    }

}