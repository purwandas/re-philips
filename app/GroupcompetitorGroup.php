<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class GroupcompetitorGroup extends Model
{
    //

    // protected $table = 'groupcompetitor_groups';
    
    protected $fillable = [
        'groupcompetitor_id', 'group_id', 
    ];

    /* Metode tambahan untuk model Branch Sport. */

    /**
     * Relation Method(s).
     *
     */

    public function groupcompetitor()
    {
        return $this->belongsTo('App\GroupCompetitor', 'groupcompetitor_id');
    }

    public function group()
    {
        return $this->belongsTo('App\Group', 'group_id');
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
