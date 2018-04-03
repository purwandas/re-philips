<?php

namespace App\Filters;

use App\Area;
use Illuminate\Http\Request;

class AttendanceFilters extends QueryFilters
{


    // Order by region
    public function byRegion($value) {
        return $this->builder->whereHas('attendanceDetails.store.district.area.region', function ($query) use ($value) {
            return $query->where('regions.id',$value);
        });
    }

    // Order by region
    public function byArea($value) {
        return $this->builder->whereHas('attendanceDetails.store.district.area', function ($query) use ($value) {
            return $query->where('areas.id',$value);
        });
    }

    // Ordering by district
    public function byDistrict($value) {
        return $this->builder->whereHas('attendanceDetails.store.districts', function ($query) use ($value) {
            return $query->where('districts.id',$value);
        });
    }

    // Ordering by store
    public function byStore($value) {
        return $this->builder->whereHas('attendanceDetails.store', function ($query) use ($value) {
            return $query->where('stores.id',$value);
        });
    }

    // Ordering by employee
    public function byEmployee($value) {
        return $this->builder->whereHas('attendanceDetails.store.employeeStores', function ($query) use ($value) {
            return $query->where('employee_stores.user_id',$value);
        });
    }

}