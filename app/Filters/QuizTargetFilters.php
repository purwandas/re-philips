<?php

namespace App\Filters;

use App\News;
use Illuminate\Http\Request;

class QuizTargetFilters extends QueryFilters
{

    /**
     * Ordering data by judul
     */
    public function target($value) {

        if(!$this->requestAllData($value)){
        	$this->builder->where(function ($query) use ($value){
                return $query->where('role', 'like', '%'.$value.'%')->orWhere('grading', 'like', '%'.$value.'%');
            });
        }

        return null;
    } 

}