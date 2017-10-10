<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class CompetitorActivityDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'competitoractivity_id', 'groupcompetitor_id'
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

    public function competitorActivity()
    {
        return $this->belongsTo('App\CompetitorActivity', 'competitoractivity_id');
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
