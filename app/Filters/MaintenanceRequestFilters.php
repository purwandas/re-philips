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

    // // Ordering by month
    // public function searchMonth($value) {
    //    return $this->builder->whereMonth('maintenance_requests.date', '=', Carbon::parse($value)->format('m'))
    //                      ->whereYear('maintenance_requests.date', '=', Carbon::parse($value)->format('Y'));
    // }

}