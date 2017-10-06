<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class PosmActivityDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'posmactivity_id', 'posm_id', 'quantity', 'photo'
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

    public function posmActivity()
    {
        return $this->belongsTo('App\PsomActivity', 'posmactivity_id');
    }

    public function posm()
    {
        return $this->belongsTo('App\Posm', 'posm_id');
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
