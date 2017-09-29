<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;


class Store extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_id', 'store_name_1', 'store_name_2', 'longitude', 'latitude', 'channel', 'account_id', 'areaapp_id', 'employee_id'
    ];

    /* Metode tambahan untuk model Branch Sport. */

    /**
     * Relation Method(s).
     *
     */

    public function account()
    {
        return $this->belongsTo('App\Account', 'account_id');
    }

    public function areaapp()
    {
        return $this->belongsTo('App\AreaApp', 'areaapp_id');
    }

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'employee_id');
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
