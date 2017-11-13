<?php

namespace App\Filters;

use App\News;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SellinFilters extends QueryFilters
{

    /**
     * Ordering data by region
     */
    public function byRegion($value) {
        return $this->builder->whereHas('sellIn.store.district.area.region', function ($query) use ($value) {
            return $query->where('regions.id',$value);
        });
    }

    // Ordering by area
    public function byArea($value) {
        return $this->builder->whereHas('sellIn.store.district.area', function ($query) use ($value) {
            return $query->where('areas.id',$value);
        });
    }

    // Ordering by district
    public function byDistrict($value) {
        return $this->builder->whereHas('sellIn.store.district', function ($query) use ($value) {
            return $query->where('districts.id',$value);
        });
    }

    // Ordering by store
    public function byStore($value) {
        return $this->builder->whereHas('sellIn.store', function ($query) use ($value) {
            return $query->where('stores.id',$value);
        });
    }

    // Ordering by employee
    public function byEmployee($value) {
        return $this->builder->whereHas('sellIn.user', function ($query) use ($value) {
            return $query->where('users.id',$value);
        });
    }

    // Ordering by month
    public function searchMonth($value) {
       return $this->builder->whereHas('sellIn', function ($query) use ($value) {
            return $query->whereMonth('sell_ins.date', '=', Carbon::parse($value)->format('m'))
                         ->whereYear('sell_ins.date', '=', Carbon::parse($value)->format('Y'));
        });
    }

}