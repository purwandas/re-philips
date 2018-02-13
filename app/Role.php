<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class Role extends Model
{
    use SoftDeletes;

    //
    protected $fillable = [
        'role', 'role_group', 
    ];

    /*
        Roles Relation
    */
    public function user()
    {
        return $this->hasMany('App\User', 'role_id');
    }

    public function quizTarget()
    {
        return $this->hasMany('App\QuizTarget', 'role_id');
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

	/* Metode tambahan untuk model Branch Sport. */

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
