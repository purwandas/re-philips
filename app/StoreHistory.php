<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class StoreHistory extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_id', 'store_re_id', 'store_name_1', 'store_name_2', 'dedicate', 'longitude', 'latitude', 'channel', 'subchannel_id', 'district_id', 'user_id', 'address', 'classification_id', 'no_telp_toko', 'no_telp_pemilik_toko', 'kepemilikan_toko', 'lokasi_toko','tipe_transaksi_2','tipe_transaksi', 'kondisi_toko', 'photo'
    ];

    /* Metode tambahan untuk model Branch Sport. */

    /**
     * Relation Method(s).
     *
     */

    public function store()
    {
        return $this->belongsTo('App\Store', 'store_id');
    }

    public function subChannel()
    {
        return $this->belongsTo('App\SubChannel', 'subchannel_id');
    }

    public function district()
    {
        return $this->belongsTo('App\District', 'district_id');
    }

    public function classification()
    {
        return $this->belongsTo('App\Classification', 'classification_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function employeeStores()
    {
        return $this->hasMany('App\EmployeeStore', 'store_id', 'storeId');
    }

    public function storeDistributors()
    {
        return $this->hasMany('App\StoreDistributor', 'store_id', 'storeId');
    }

    public function spvDemo()
    {
        return $this->hasOne('App\SpvDemo', 'store_id', 'storeId');
    }

    // Transactions

    public function sellInTransactions(){
        return $this->hasMany('App\SellIn', 'store_id', 'storeId');
    }

    public function sellOutTransactions(){
        return $this->hasMany('App\SellOut', 'store_id', 'storeId');
    }

    public function retDistributorTransactions(){
        return $this->hasMany('App\RetDitributor', 'store_id', 'storeId');
    }

    public function retConsumentTransactions(){
        return $this->hasMany('App\RetConsument', 'store_id', 'storeId');
    }

    public function freeProductTransactions(){
        return $this->hasMany('App\FreeProduct', 'store_id', 'storeId');
    }

    public function tbatTransactions(){
        return $this->hasMany('App\Tbat', 'store_id', 'storeId');
    }

    public function sohTransactions(){
        return $this->hasMany('App\SOH', 'store_id', 'storeId');
    }

    public function sosTransactions(){
        return $this->hasMany('App\Sos', 'store_id', 'storeId');
    }

    public function posmActivityTransactions(){
        return $this->hasMany('App\PosmActivity', 'store_id', 'storeId');
    }

    public function competitorActivityTransactions(){
        return $this->hasMany('App\CompetitorActivity', 'store_id', 'storeId');
    }

    public function promoActivityTransactions(){
        return $this->hasMany('App\PromoActivity', 'store_id', 'storeId');
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

    public function getGlobalChannelIdAttribute(){

        return ($this->attributes['subchannel_id']) ? $this->subChannel->channel->globalChannel->id : '';

    }
}
