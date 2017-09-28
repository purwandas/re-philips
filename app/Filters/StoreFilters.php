<?php

namespace App\Filters;

use App\Store;
use Illuminate\Http\Request;

class StoreFilters extends QueryFilters
{

    /**
     * Ordering data by store name
     */
    public function store($value) {
        if(!$this->requestAllData($value)){
        	$this->builder->where(function ($query) use ($value){
                return $query->where('store_name_1', 'like', '%'.$value.'%')->orWhere('store_name_2', 'like', '%'.$value.'%')->orWhere('store_id', 'like', '%'.$value.'%');
            });
        }

        return null;
    } 

}