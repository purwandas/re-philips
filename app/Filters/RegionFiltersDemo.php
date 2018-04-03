<?php

namespace App\Filters;

use App\Region;
use Illuminate\Http\Request;

class RegionFiltersDemo extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%') : null;
    }

    // Ordering by area
    public function byAreaDemo($value) {
        return $this->builder->whereHas('areas', function ($query) use ($value) {
            return $query->where('areas.id',$value);
        });
    }

//    // Ordering by area app
//    public function byAreaApp($value) {
//        return $this->builder->whereHas('areas.areaApps', function ($query) use ($value) {
//            return $query->where('area_apps.id',$value);
//        });
//    }

    // Ordering by district
    public function byDistrictDemo($value) {
        return $this->builder->whereHas('areas.districts', function ($query) use ($value) {
            return $query->where('districts.id',$value);
        });
    }

    // Ordering by store
    public function byStoreDemo($value) {
        return $this->builder->whereHas('areas.districts.stores', function ($query) use ($value) {
            return $query->where('stores.id',$value);
        });
    }

    // Ordering by employee
    public function byEmployeeDemo($value) {
        return $this->builder->whereHas('areas.districts.stores.employeeStores', function ($query) use ($value) {
            return $query->where('employee_stores.user_id',$value);
        });
    }

}