<?php

namespace App\Filters;

use App\User;
use Illuminate\Http\Request;

class UserFiltersDemo extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%') : null;
    }

    // Ordering by store
    public function byStoreDemo($value) {
        return $this->builder->whereHas('employeeStores.store', function ($query) use ($value) {
            return $query->where('stores.id',$value);
        });
    }

    /**
     * Ordering data by name
     */
    public function byName($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('users.id', $value) : null;
    }

    // Ordering by district
    public function byDistrictDemo($value) {
        return $this->builder->whereHas('employeeStores.store.district', function ($query) use ($value) {
            return $query->where('districts.id',$value);
        });
    }

    // Ordering by area
    public function byAreaDemo($value) {
        return $this->builder->whereHas('employeeStores.store.district.area', function ($query) use ($value) {
            return $query->where('areas.id',$value);
        });
    }

    // Ordering by region
    public function byRegionSpv($value) {
        return $this->builder->whereHas('employeeStores.store.district.area.region', function ($query) use ($value) {
            return $query->where('regions.id',$value);
        });
    }

}