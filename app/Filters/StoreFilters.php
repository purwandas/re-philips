<?php

namespace App\Filters;

use App\Store;
use Illuminate\Http\Request;

class StoreFilters extends QueryFilters
{

    /**
     * Ordering data by store name
     */
    public function store($value) {
        if(!$this->requestAllData($value)){
        	$this->builder->where(function ($query) use ($value){
                return $query->where('store_name_1', 'like', '%'.$value.'%')->orWhere('store_name_2', 'like', '%'.$value.'%')->orWhere('store_id', 'like', '%'.$value.'%');
            });
        }

        return null;
    }


    // Order by region
    public function byRegion($value) {
        return $this->builder->whereHas('district.area.region', function ($query) use ($value) {
            return $query->where('regions.id',$value);
        });
    }

    // Ordering by area
    public function byArea($value) {
        return $this->builder->whereHas('district.area', function ($query) use ($value) {
            return $query->where('areas.id',$value);
        });
    }

    // Ordering by spv
    public function bySpv($value) {
        return $this->builder->where('user_id',null)->orwhere('user_id',$value);
    }

    // get all different store ID
    public function bySpvNew() {
        return $this->builder->groupBy('store_id');
    }

    // Ordering by dedicate
    public function byDedicate($value) {
        return $this->builder->where('dedicate',$value);//->orwhere('user_id',$value);
    }

    // Ordering by dedicate SPV
    public function byDedicateSpv($value) {
        
        if ($value=="HYBRID") {
            return $this->builder->whereIn('dedicate', ['DA','PC','HYBRID']);
        }else if ($value=="DA"){
            return $this->builder->where(function ($query) {
                return $query->whereIn('dedicate',['DA','HYBRID'])
                            ->orwhereNull('dedicate');
            });
        }else if ($value=="PC"){
            return $this->builder->where(function ($query) {
                return $query->whereIn('dedicate',['PC','HYBRID'])
                            ->orwhereNull('dedicate');
            });
        }else{
            return $this->builder->where(function ($query) {
                return $query->where('dedicate',$value)
                            ->orwhereNull('dedicate');
            });
        }
        // return $this->builder->whereNull('dedicate')->orwhere('dedicate',$value);
    }


//    // Ordering by area
//    public function byAreaApp($value) {
//        return $this->builder->whereHas('areaapp', function ($query) use ($value) {
//            return $query->where('area_apps.id',$value);
//        });
//    }

    // Ordering by district
    public function byDistrict($value) {
        return $this->builder->whereHas('district', function ($query) use ($value) {
            return $query->where('districts.id',$value);
        });
    }

    // Ordering by employee
    public function byEmployee($value) {
        return $this->builder->whereHas('employeeStores', function ($query) use ($value) {
            return $query->where('employee_stores.user_id',$value);
        });
    }

}