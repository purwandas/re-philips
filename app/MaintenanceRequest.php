<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class MaintenanceRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'region_id', 'area_id', 'store_id', 'category', 'channel', 'type', 'date', 'photo', 'report'
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

    public function region()
    {
        return $this->belongsTo('App\Region', 'region_id');
    }

    public function area()
    {
        return $this->belongsTo('App\Area', 'area_id');
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
