<?php

namespace App\Filters;

use App\Account;
use Illuminate\Http\Request;

class SubChannelFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('sub_channels.name', 'like', '%'.$value.'%') : null;
    }

}