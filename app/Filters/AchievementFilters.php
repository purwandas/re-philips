<?php

namespace App\Filters;

use App\News;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AchievementFilters extends QueryFilters
{

    /**
     * Ordering data by region
     */
    public function byRegion($value) {
        return $this->builder->where('region_id',$value);
    }

    // Ordering by area
    public function byArea($value) {
        return $this->builder->where('area_id',$value);
    }

    // Ordering by district
    public function byDistrict($value) {
        return $this->builder->where('district_id',$value);
    }

    // Ordering by store
    public function byStore($value) {
        return $this->builder->where('storeId',$value);
    }

    // Ordering by employee
    public function byEmployee($value) {
        return $this->builder->where('user_id',$value);
    }

    // Ordering by month
    public function searchMonth($value) {
        $this->builder->whereMonth('created_at', '=', Carbon::parse($value)->format('m'))
                     ->whereYear('created_at', '=', Carbon::parse($value)->format('Y'));
    }

}