<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class Price extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id', 'globalchannel_id', 'sell_type', 'price', 'release_date'
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

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function globalChannel()
    {
        return $this->belongsTo('App\GlobalChannel', 'globalchannel_id');
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
