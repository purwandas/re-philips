<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class GroupCompetitor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'kategori', 'groupproduct_id'
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

    public function groupProduct()
    {
        return $this->belongsTo('App\GroupProduct', 'groupproduct_id');
    }

    public function competitorActivityDetails()
    {
        return $this->hasMany('App\CompetitorActivityDetail', 'groupcompetitor_id');
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
