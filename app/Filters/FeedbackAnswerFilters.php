<?php

namespace App\Filters;

use App\FeedbackAnswer;
use Illuminate\Http\Request;


class FeedbackAnswerFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    // public function assessor($value) {
    //     return (!$this->requestAllData($value)) ? $this->builder->where('assessor_id', 'like', '%'.$value.'%') 
    //                 ->join('users as assessors', 'feedback_answers.assessor_id', '=', 'assessors.id')
    //                 ->select('feedback_answers.*', 'assessors.name as assessor_name') : null;
    // }

    public function question($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('question', 'like', '%'.$value.'%') : null;
    }
}