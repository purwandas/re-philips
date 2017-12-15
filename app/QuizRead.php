<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuizRead extends Model
{
    protected $fillable = [
        'quiz_id', 'user_id'
    ];

    /**
     * Relation Method(s).
     *
     */

    public function quiz()
    {
        return $this->belongsTo('App\Quiz', 'quiz_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
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
