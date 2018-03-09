<?php

namespace App\Filters;

use App\Area;
use Illuminate\Http\Request;

class ApmFilters extends QueryFilters
{

    /**
     * Ordering data
     */

    // Ordering by store
    public function byStore($value) {
        return $this->builder->whereHas('stores', function ($query) use ($value) {
            return $query->where('stores.id',$value);
        });
    }

}