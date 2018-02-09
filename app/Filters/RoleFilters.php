<?php

namespace App\Filters;

use App\Area;
use Illuminate\Http\Request;

class RoleFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function role($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('role', 'like', '%'.$value.'%') : null;
    }

    public function nonMaster($value) {
        return (!$this->requestAllData($value)) ? $this->builder->whereNotIn('role_group',['Master']) : null;
    }

    public function promoterGroup($value) {
    	$roles = ['Promoter','Promoter Additional','Promoter Event','Demonstrator MCC','Demonstrator DA','ACT','PPE','BDT','Salesman Explorer','SMD','SMD Coordinator','HIC','HIE','SMD Additional','ASC'];
        return (!$this->requestAllData($value)) ? $this->builder->whereIn('role_group',$roles) : null;
    }

    public function nonPromoterGroup($value) {
    	$roles = ['Promoter','Promoter Additional','Promoter Event','Demonstrator MCC','Demonstrator DA','ACT','PPE','BDT','Salesman Explorer','SMD','SMD Coordinator','HIC','HIE','SMD Additional','ASC'];
        return (!$this->requestAllData($value)) ? $this->builder->whereNotIn('role_group',$roles) : null;
    }

}