<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class Channel extends Model
{
    use SoftDeletes;

    //
    protected $fillable = [
        'name', 'globalchannel_id'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

	/* Metode tambahan untuk model Branch Sport. */

	/**
     * Relation Method(s).
     *
     */

	public function subChannels()
    {
        return $this->hasMany('App\SubChannel', 'channel_id');
    }

    public function globalChannel()
    {
        return $this->belongsTo('App\GlobalChannel', 'globalchannel_id');
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
