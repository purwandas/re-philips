<?php

namespace App\Filters;

use App\News;
use Illuminate\Http\Request;

class QuizFilters extends QueryFilters
{

    /**
     * Ordering data by judul
     */
    public function judul($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('judul', 'like', '%'.$value.'%') : null;
    } 

    public function deskripsi($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('deskripsi', 'like', '%'.$value.'%') : null;
    } 

}