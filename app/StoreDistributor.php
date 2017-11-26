<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class StoreDistributor extends Model
{
    protected $fillable = [
        'store_id', 'distributor_id'
    ];

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
