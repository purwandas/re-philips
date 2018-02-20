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

    // Ordering by store
    public function byStore($value) {
        return $this->builder->whereHas('store', function ($query) use ($value) {
            return $query->where('stores.id',$value);
        });
    }

    // Ordering by employee
    public function byEmployee($value) {
        return $this->builder->whereHas('user', function ($query) use ($value) {
            return $query->where('users.id',$value);
        });
    }

    // Ordering by sell type
    public function bySellType($value) {
        return $this->builder->where('sell_type', $value);
    }

}