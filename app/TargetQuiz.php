<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class TargetQuiz extends Model
{
    
    protected $table = 'target_quizs';

    protected $fillable = [
        'quiz_target_id', 'quiz_id'
    ];

	/* Metode tambahan untuk model Branch Sport. */

	/**
     * Relation Method(s).
     *
     */

	public function quiz()
    {
        return $this->belongsTo('App\Quiz', 'quiz_id');
    }

    public function target()
    {
        return $this->belongsTo('App\QuizTarget', 'quiz_target_id');
    }

	/**
     * Filtering Berdasarakan Request User
     * @param $query
     * @param QueryFilters $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
