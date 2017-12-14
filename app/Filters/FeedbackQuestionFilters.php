<?php

namespace App\Filters;

use App\FeedbackQuestion;
use Illuminate\Http\Request;

class FeedbackQuestionFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function question($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('question', 'like', '%'.$value.'%') : null;
    }

}