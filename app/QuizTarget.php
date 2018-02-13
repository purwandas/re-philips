<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class QuizTarget extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'role_id', 'grading_id'
    ];

    /**
     * Relation Method(s).
     *
     */

    public function role()
    {
        return $this->belongsTo('App\Role', 'role_id');
    }

    public function grading()
    {
        return $this->belongsTo('App\Grading', 'grading_id');
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
