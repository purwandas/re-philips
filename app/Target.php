<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class Target extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'store_id', 'sell_type','target_da', 'target_pf_da', 'target_pc', 'target_pf_pc', 'target_mcc', 'target_pf_mcc'
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

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
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
