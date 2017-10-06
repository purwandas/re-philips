<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class NewsRead extends Model
{
    protected $fillable = [
        'news_id', 'user_id'
    ];

    /**
     * Relation Method(s).
     *
     */

    public function news()
    {
        return $this->belongsTo('App\News', 'news_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
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
