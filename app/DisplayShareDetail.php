<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class DisplayShareDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'display_share_id', 'category_id', 'philips', 'all'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /* Metode tambahan untuk model. */

    /**
     * Relation Method(s).
     *
     */

    public function displayShare()
    {
        return $this->belongsTo('App\DisplayShare', 'display_share_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
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
