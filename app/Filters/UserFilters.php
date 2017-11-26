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

    public function employee($value) {
        if(!$this->requestAllData($value)){
        	$this->builder->where(function ($query) use ($value){
                return $query->where('name', 'like', '%'.$value.'%')->orWhere('nik', 'like', '%'.$value.'%');
            });
        }
    }

    /* Ordering data by role */
    public function role($value){
    	return $this->builder->where('role', $value);
    }

    /* Ordering data by role group */
    public function roleGroup($value){
        return $this->builder->whereIn('role', $value);
    }

    // Ordering by store
    public function byStore($value) {
        return $this->builder->whereHas('employeeStores.store', function ($query) use ($value) {
            return $query->where('stores.id',$value);
        });
    }

//    // Ordering by area app
//    public function byAreaApp($value) {
//        return $this->builder->whereHas('employeeStores.store.areaapp', function ($query) use ($value) {
//            return $query->where('area_apps.id',$value);
//        });
//    }

    // Ordering by district
    public function byDistrict($value) {
        return $this->builder->whereHas('employeeStores.store.district', function ($query) use ($value) {
            return $query->where('districts.id',$value);
        });
    }

    // Ordering by area
    public function byArea($value) {
        return $this->builder->whereHas('employeeStores.store.district.area', function ($query) use ($value) {
            return $query->where('areas.id',$value);
        });
    }

    // Ordering by region
    public function byRegion($value) {
        return $this->builder->whereHas('employeeStores.store.district.area.region', function ($query) use ($value) {
            return $query->where('regions.id',$value);
        });
    }

}