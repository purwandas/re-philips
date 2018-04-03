<?php

namespace App\Filters;

use App\User;
use Illuminate\Http\Request;

class UserFiltersSpv extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%') : null;
    }

    // Ordering by store
    public function byStoreSpv($value) {
        return $this->builder->whereHas('stores', function ($query) use ($value) {
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
    public function byDistrictSpv($value) {
        return $this->builder->whereHas('stores.district', function ($query) use ($value) {
            return $query->where('districts.id',$value);
        });
    }

    // Ordering by area
    public function byAreaSpv($value) {
        return $this->builder->whereHas('stores.district.area', function ($query) use ($value) {
            return $query->where('areas.id',$value);
        });
    }

    // Ordering by region
    public function byRegionSpv($value) {
        return $this->builder->whereHas('stores.district.area.region', function ($query) use ($value) {
            return $query->where('regions.id',$value);
        });
    }

}