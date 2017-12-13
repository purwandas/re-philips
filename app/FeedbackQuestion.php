<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class FeedbackQuestion extends Model
{
    use SoftDeletes;

    protected $fillable = [
    	'feedbackCategory_id','question','type',
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
    public function feedbackCategory()
    {
        return $this->belongsTo('App\feedbackCategory', 'feedbackCategory_id');
    }

    public function feedbackAnswer()
    {
        return $this->hasMany('App\feedbackAnswer', 'feedbackQuestion_id');
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

