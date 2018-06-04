<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;
use Carbon\Carbon;

class SellOut extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'store_id', 'week', 'date'
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

    public function sellOutDetails()
    {
        return $this->hasMany('App\SellOutDetail', 'sellout_id');
    }

    public function store()
    {
        return $this->belongsTo('App\Store', 'store_id');
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

    /**
     *
     * Eager load attribute
     *
     **/

    public function getWeekAttribute(){

        return Carbon::parse($this->attributes['date'])->weekOfMonth;

    }

    // public function getChannelAttribute(){

    //     return $this->store->subChannel;

    // }

    public function getAmountAttribute(){

        return $this->sellOutDetails()->get()->sum('amount');

    }

}
