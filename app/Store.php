<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;


class Store extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_id', 'store_name_1', 'store_name_2', 'longitude', 'latitude', 'channel', 'account_id', 'areaapp_id', 'user_id'
    ];

    /* Metode tambahan untuk model Branch Sport. */

    /**
     * Relation Method(s).
     *
     */

    public function account()
    {
        return $this->belongsTo('App\Account', 'account_id');
    }

    public function areaapp()
    {
        return $this->belongsTo('App\AreaApp', 'areaapp_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function employeeStores()
    {
        return $this->hasMany('App\EmployeeStore', 'store_id');
    }

    // Transactions

    public function sellInTransactions(){
        return $this->hasMany('App\SellIn', 'store_id');
    }

    public function sellOutTransactions(){
        return $this->hasMany('App\SellOut', 'store_id');
    }

    public function retDistributorTransactions(){
        return $this->hasMany('App\RetDitributor', 'store_id');
    }

    public function retConsumentTransactions(){
        return $this->hasMany('App\RetConsument', 'store_id');
    }

    public function freeProductTransactions(){
        return $this->hasMany('App\FreeProduct', 'store_id');
    }

    public function tbatTransactions(){
        return $this->hasMany('App\Tbat', 'store_id');
    }

    public function sohTransactions(){
        return $this->hasMany('App\SOH', 'store_id');
    }

    public function sosTransactions(){
        return $this->hasMany('App\Sos', 'store_id');
    }

    public function posmActivityTransactions(){
        return $this->hasMany('App\PosmActivity', 'store_id');
    }

    public function competitorActivityTransactions(){
        return $this->hasMany('App\CompetitorActivity', 'store_id');
    }

    public function promoActivityTransactions(){
        return $this->hasMany('App\PromoActivity', 'store_id');
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
