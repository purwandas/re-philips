<?php

namespace App\Filters;

use App\Area;
use Illuminate\Http\Request;

class AttendanceFiltersDemo extends QueryFilters
{


    // Order by region
    public function byRegionDemo($value) {
        return $this->builder->whereHas('attendanceDetails.store.district.area.region', function ($query) use ($value) {
            return $query->where('regions.id',$value);
        });
    }

    // Order by region
    public function byAreaDemo($value) {
        return $this->builder->whereHas('attendanceDetails.store.district.area', function ($query) use ($value) {
            return $query->where('areas.id',$value);
        });
    }

    // Ordering by district
    public function byDistrictDemo($value) {
        return $this->builder->whereHas('attendanceDetails.store.districts', function ($query) use ($value) {
            return $query->where('districts.id',$value);
        });
    }

    // Ordering by store
    public function byStoreDemo($value) {
        return $this->builder->whereHas('attendanceDetails.store', function ($query) use ($value) {
            return $query->where('stores.id',$value);
        });
    }

    // Ordering by employee
    public function byEmployeeDemo($value) {
        return $this->builder->whereHas('user.spvDemos', function ($query) use ($value) {
            return $query->where('spv_demos.user_id',$value);
        });
    }

}