<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class Apm extends Model
{
    use SoftDeletes;

    //
    protected $fillable = [
        'store_id', 'product_id', 'month_minus_3_value', 'month_minus_2_value', 'month_minus_1_value', 'month_minus_4_value', 'month_minus_5_value', 'month_minus_6_value',
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

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
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
