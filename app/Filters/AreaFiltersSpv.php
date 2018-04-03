<?php

namespace App\Filters;

use App\Area;
use Illuminate\Http\Request;

class AreaFiltersSpv extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('areas.name', 'like', '%'.$value.'%') : null;
    }

    // Order by region
    public function byRegionSpv($value) {
        return $this->builder->whereHas('region', function ($query) use ($value) {
            return $query->where('regions.id',$value);
        });
    }

    // Ordering by district
    public function byDistrictSpv($value) {
        return $this->builder->whereHas('districts', function ($query) use ($value) {
            return $query->where('districts.id',$value);
        });
    }

    // Ordering by store
    public function byStoreSpv($value) {
        return $this->builder->whereHas('districts.stores', function ($query) use ($value) {
            return $query->where('stores.id',$value);
        });
    }

    // Ordering by employee
    public function byEmployeeSpv($value) {
        return $this->builder->whereHas('districts.stores.employeeStores', function ($query) use ($value) {
            return $query->where('employee_stores.user_id',$value);
        });
    }

}