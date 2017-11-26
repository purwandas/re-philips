<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class CompetitorActivity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'store_id', 'week', 'date', 'sku', 'groupcompetitor_id', 'promo_type', 'information', 'start_period', 'end_period', 'photo'
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

    public function competitorActivityDetails()
    {
        return $this->hasMany('App\CompetitorActivityDetail', 'competitoractivity_id');
    }

    public function store()
    {
        return $this->belongsTo('App\Store', 'store_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function groupCompetitor()
    {
        return $this->belongsTo('App\GroupCompetitor', 'groupcompetitor_id');
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
