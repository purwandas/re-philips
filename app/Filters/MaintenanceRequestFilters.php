<?php

namespace App\Filters;

use App\News;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MaintenanceRequestFilters extends QueryFilters
{

    /**
     * Ordering data by report description
     */

    // public function byRegion($value) {
    //     return $this->builder->whereHas('maintenanceRequest.store.district.area.region', function ($query) use ($value) {
    //         return $query->where('regions.id',$value);
    //     });
    // }

    // // Ordering by area
    // public function byArea($value) {
    //     return $this->builder->whereHas('maintenanceRequest.store.district.area', function ($query) use ($value) {
    //         return $query->where('areas.id',$value);
    //     });
    // }

    // // Ordering by district
    // public function byDistrict($value) {
    //     return $this->builder->whereHas('maintenanceRequest.store.district', function ($query) use ($value) {
    //         return $query->where('districts.id',$value);
    //     });
    // }

    public function byReport($value) {
        return $this->builder->where('report','like','%'.$value.'%');
    }

    // // Ordering by store
    // public function byStore($value) {
    //     return $this->builder->where('maintenance_requests.store_id',$value);
    // }

    // // Ordering by employee
    // public function byEmployee($value) {
    //     return $this->builder->where('maintenance_requests.user_id',$value);
    // }

    // Ordering by month
    // public function searchMonth($value) {
    //    return $this->builder->whereMonth('maintenance_requests.date', '=', Carbon::parse($value)->format('m'));
    //                      // ->whereYear('maintenance_requests.date', '=', Carbon::parse($value)->format('Y'));
    // }

}