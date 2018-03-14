<?php

namespace App\Filters;

use App\Area;
use Illuminate\Http\Request;

class ApmFilters extends QueryFilters
{

    /**
     * Ordering data
     */

    // Ordering by Region
    public function byRegion($value) {
        return $this->builder->whereHas('store', function ($query) use ($value) {
            return $query->where('regions.id',$value);
        });
    }

    // Ordering by Area
    public function byArea($value) {
        return $this->builder->whereHas('store', function ($query) use ($value) {
            return $query->where('areas.id',$value);
        });
    }
    
    // Ordering by District
    public function byDistrict($value) {
        return $this->builder->whereHas('store', function ($query) use ($value) {
            return $query->where('districts.id',$value);
        });
    }

    // Ordering by store
    public function byStore($value) {
        return $this->builder->whereHas('store', function ($query) use ($value) {
            return $query->where('stores.id',$value);
        });
    }

    // Ordering by product
    public function byProduct($value) {
        return $this->builder->whereHas('product', function ($query) use ($value) {
            return $query->where('products.id',$value);
        });
    }

}