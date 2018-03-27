<?php

namespace App\Filters;

use App\User;
use Illuminate\Http\Request;

class UserFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%') : null;
    }

    public function employee($value) {
        if(!$this->requestAllData($value)){
            $this->builder->where(function ($query) use ($value){
                return $query->where('users.name', 'like', '%'.$value.'%')->orWhere('users.nik', 'like', '%'.$value.'%');
            });
        }
    }

    public function employee2($value) {
        if(!$this->requestAllData($value)){
            $this->builder->where(function ($query) use ($value){
                return $query->where('users.name', 'like', '%'.$value.'%')->orWhere('users.nik', 'like', '%'.$value.'%');
            });
        }
    }

    public function notInId($value){
        return $this->builder->where('users.id', '<>', $value);
    }

    /* Ordering data by role */
    public function role($value){
    	return $this->builder->where('role', $value);
    }

    /* Ordering data by role group */
    public function roleGroup($value){
        return $this->builder->whereHas('role', function ($query) use ($value) {
            return $query->whereIn('roles.role_group',$value);
        });
        // return $this->builder->whereIn('role', $value);
    }

    public function promoterGroup($value) {
        $roles = ['Promoter','Promoter Additional','Promoter Event','Demonstrator MCC','Demonstrator DA','ACT','PPE','BDT','Salesman Explorer','SMD','SMD Coordinator','HIC','HIE','SMD Additional','ASC'];
        return $this->builder->whereHas('role', function ($query) use ($roles) {
            return $query->whereIn('roles.role_group',$roles);
        });
    }

    public function promoterGroupNew($value) {
        $roles = ['Promoter','Promoter Additional','Promoter Event', 'Demonstrator MCC','Demonstrator DA','ACT','PPE','BDT','SMD','SMD Coordinator','HIC','HIE','SMD Additional','ASC'];
        return $this->builder->whereHas('role', function ($query) use ($roles) {
            return $query->whereIn('roles.role_group',$roles);
        });
    }

    public function noDemo($value) {
        $roles = ['Promoter','Promoter Additional','Promoter Event','ACT','PPE','BDT','Salesman Explorer','SMD','SMD Coordinator','HIC','HIE','SMD Additional','ASC'];
        return $this->builder->whereHas('role', function ($query) use ($roles) {
            return $query->whereIn('roles.role_group',$roles);
        });
    }

    // Ordering by store
    public function byStore($value) {
        return $this->builder->whereHas('employeeStores.store', function ($query) use ($value) {
            return $query->where('stores.id',$value);
        });
    }

//    // Ordering by area app
//    public function byAreaApp($value) {
//        return $this->builder->whereHas('employeeStores.store.areaapp', function ($query) use ($value) {
//            return $query->where('area_apps.id',$value);
//        });
//    }

    /**
     * Ordering data by name
     */
    public function byName($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('users.id', $value) : null;
    }

    // Ordering by district
    public function byDistrict($value) {
        return $this->builder->whereHas('employeeStores.store.district', function ($query) use ($value) {
            return $query->where('districts.id',$value);
        });
    }

    // Ordering by area
    public function byArea($value) {
        return $this->builder->whereHas('employeeStores.store.district.area', function ($query) use ($value) {
            return $query->where('areas.id',$value);
        });
    }

    // Ordering by region
    public function byRegion($value) {
        return $this->builder->whereHas('employeeStores.store.district.area.region', function ($query) use ($value) {
            return $query->where('regions.id',$value);
        });
    }

    // Ordering by Role
    public function byRole($value){
        return $this->builder->whereHas('role', function ($query) use ($value) {
            return $query->where('roles.role_group',$value);
        });
    }

    public function noAdmin($value){
        return $this->builder->whereHas('role', function ($query) use ($value) {
            return $query->whereNotIn('roles.role_group',$value);
        });
    }

}