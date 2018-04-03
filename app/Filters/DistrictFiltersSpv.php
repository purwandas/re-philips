<?php

namespace App\Filters;

use App\District;
use Illuminate\Http\Request;

class DistrictFiltersSpv extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('districts.name', 'like', '%'.$value.'%') : null;
    }

    // Order by region
    public function byRegionSpv($value) {
        return $this->builder->whereHas('area.region', function ($query) use ($value) {
            return $query->where('regions.id',$value);
        });
    }

    // Ordering by area
    public function byAreaSpv($value) {
        return $this->builder->whereHas('area', function ($query) use ($value) {
            return $query->where('areas.id',$value);
        });
    }

    // Ordering by store
    public function byStoreSpv($value) {
        return $this->builder->whereHas('stores', function ($query) use ($value) {
            return $query->where('stores.id',$value);
        });
    }

    // Ordering by employee
    public function byEmployeeSpv($value) {
        return $this->builder->whereHas('stores.employeeStores', function ($query) use ($value) {
            return $query->where('employee_stores.user_id',$value);
        });
    }

}