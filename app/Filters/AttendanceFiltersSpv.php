<?php

namespace App\Filters;

use App\Area;
use Illuminate\Http\Request;

class AttendanceFiltersSpv extends QueryFilters
{


    // Order by region
    public function byRegionSpv($value) {
        return $this->builder->whereHas('attendanceDetails.store.district.area.region', function ($query) use ($value) {
            return $query->where('regions.id',$value);
        });
    }

    // Order by region
    public function byAreaSpv($value) {
        return $this->builder->whereHas('attendanceDetails.store.district.area', function ($query) use ($value) {
            return $query->where('areas.id',$value);
        });
    }

    // Ordering by district
    public function byDistrictSpv($value) {
        return $this->builder->whereHas('attendanceDetails.store.districts', function ($query) use ($value) {
            return $query->where('districts.id',$value);
        });
    }

    // Ordering by store
    public function byStoreSpv($value) {
        return $this->builder->whereHas('attendanceDetails.store', function ($query) use ($value) {
            return $query->where('stores.id',$value);
        });
    }

    // Ordering by employee
    public function byEmployeeSpv($value) {
        return $this->builder->whereHas('attendanceDetails.store', function ($query) use ($value) {
            return $query->where('stores.user_id',$value);
        });
    }

}