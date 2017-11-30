<?php

namespace App\Filters;

use App\News;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportTbatFilters extends QueryFilters
{

    /**
     * Ordering data by region
     */
    public function byRegion($value) {
        return $this->builder->whereHas('tbat.store.areaapp.area.region', function ($query) use ($value) {
            return $query->where('regions.id',$value);
        });
    }

    // Ordering by area
    public function byArea($value) {
        return $this->builder->whereHas('tbat.store.areaapp.area', function ($query) use ($value) {
            return $query->where('areas.id',$value);
        });
    }

    // Ordering by area app
    public function byAreaApp($value) {
        return $this->builder->whereHas('tbat.store.areaapp', function ($query) use ($value) {
            return $query->where('area_apps.id',$value);
        });
    }

    // Ordering by store
    public function byStore($value) {
        return $this->builder->whereHas('tbat.store', function ($query) use ($value) {
            return $query->where('stores.id',$value);
        });
    }

    // Ordering by employee
    public function byEmployee($value) {
        return $this->builder->whereHas('tbat.user', function ($query) use ($value) {
            return $query->where('users.id',$value);
        });
    }

    // Ordering by date
    public function searchDate($value) {
       return $this->builder->whereHas('tbat', function ($query) use ($value) {
            return $query->whereDate('tbats.date', '=', Carbon::parse($value)->format('Y-m-d'));
        });
    }

    // Ordering by date range
    public function searchDateRange($value) {
        if(!isset($value[0]) || !isset($value[1])){
            return $this->builder;
        }

        return $this->builder->whereHas('tbat', function ($query) use ($value) {
            return $query->whereDate('tbats.date', '>=', Carbon::parse($value[0])->format('Y-m-d'))
                ->whereDate('tbats.date', '<=', Carbon::parse($value[1])->format('Y-m-d'));
        });
    }

    // Ordering by month
    public function searchMonth($value) {
       return $this->builder->whereHas('tbat', function ($query) use ($value) {
            return $query->whereMonth('tbats.date', '=', Carbon::parse($value)->format('m'));
        });
    }

    // Ordering by month range
    public function searchMonthRange($value) {
        if(!isset($value[0]) || !isset($value[1])){
            return $this->builder;
        }

        return $this->builder->whereHas('tbat', function ($query) use ($value) {
            return $query->whereMonth('tbats.date', '>=', Carbon::parse($value[0])->format('m'))
                ->whereMonth('tbats.date', '<=', Carbon::parse($value[1])->format('m'));
        });
    }

}