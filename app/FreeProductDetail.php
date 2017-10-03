<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class FreeProductDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'freeproduct_id', 'product_id', 'quantity'
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

    public function freeProduct()
    {
        return $this->belongsTo('App\FreeProduct', 'freeproduct_id');
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
