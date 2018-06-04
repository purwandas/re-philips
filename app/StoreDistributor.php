<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreDistributor extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'store_id', 'distributor_id'
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

	public function distributor()
    {
        return $this->belongsTo('App\Distributor', 'distributor_id');
    }

    public function store()
    {
        return $this->belongsTo('App\Store', 'store_id');
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
