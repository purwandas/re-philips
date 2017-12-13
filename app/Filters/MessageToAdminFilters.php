<?php

namespace App\Filters;

use App\Account;
use Illuminate\Http\Request;

class MessageToAdminFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function subject($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('subject', 'like', '%'.$value.'%') : null;
    }

}