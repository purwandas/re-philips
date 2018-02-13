<?php

namespace App\Filters;

use App\Area;
use Illuminate\Http\Request;

class LeadtimeFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%') : null;
    }

    // Order by region
    public function byRegion($value) {
        return $this->builder->whereHas('area.region', function ($query) use ($value) {
            return $query->where('regions.id',$value);
        });
    }

    // Ordering by area
    public function byArea($value) {
        return $this->builder->whereHas('area', function ($query) use ($value) {
            return $query->where('areas.id',$value);
        });
    }

}