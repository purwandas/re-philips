<?php

namespace App\Filters;

use App\News;
use Illuminate\Http\Request;

class PriceFilters extends QueryFilters
{

    /**
     * Ordering data by price
     */
    public function price($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('price', $value) : null;
    }

    // Ordering data by Global Channel
    public function byGChannel($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('globalchannel_id', $value) : null;
    }    

}