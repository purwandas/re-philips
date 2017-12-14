<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class FeedbackAnswer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'assessor_id','promoter_id','feedbackQuestion_id', 'answer',
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
    public function feedbackQuestion()
    {
        return $this->belongsTo('App\feedbackQuestion', 'feedbackQuestion_id');
    }

    public function assessor()
    {
        return $this->belongsTo('App\user', 'assessor_id');
    }

    public function promoter()
    {
        return $this->belongsTo('App\user', 'promoter_id');
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

