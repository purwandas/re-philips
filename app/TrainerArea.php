<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainerArea extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id', 'area_id',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

	/* Metode tambahan untuk model Branch Sport. */

	/**
     * Relation Method(s).
     *
     */

	public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function area()
    {
        return $this->belongsTo('App\Area', 'area_id');
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
