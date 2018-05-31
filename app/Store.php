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
        'store_id', 'store_name_1', 'store_name_2', 'dedicate', 'longitude', 'latitude', 'channel', 'subchannel_id', 'district_id', 'user_id', 'address', 'classification_id', 'no_telp_toko', 'no_telp_pemilik_toko', 'kepemilikan_toko', 'lokasi_toko','tipe_transaksi_2','tipe_transaksi', 'kondisi_toko', 'photo'
    ];

    /* Metode tambahan untuk model Branch Sport. */

    /**
     * Relation Method(s).
     *
     */

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
        return $this->hasMany('App\EmployeeStore', 'store_id');
    }

    public function storeDistributors()
    {
        return $this->hasMany('App\StoreDistributor', 'store_id');
    }

    public function spvDemo()
    {
        return $this->hasOne('App\SpvDemo', 'store_id');
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

     /**
     *
     * Eager load attribute
     *
     **/

    public function getGlobalChannelIdAttribute(){

        return ($this->attributes['subchannel_id']) ? $this->subChannel->channel->globalChannel->id : '';

    }

    public function getDistributorNameAttribute(){

        return ($this->storeDistributors->count() > 0) ? $this->storeDistributors()->first()->distributor->name : '';

        // if($this->storeDistributors()->count() > 0) return implode(', ', $this->storeDistributors()->with('distributor')->get()->pluck('distributor.name')->toArray());

        // return '';
    }

    public function getDistributorCodeAttribute(){

        return ($this->storeDistributors->count() > 0) ? $this->storeDistributors()->first()->distributor->code : '';

        // if($this->storeDistributors()->count() > 0) return implode(', ', $this->storeDistributors()->with('distributor')->get()->pluck('distributor.code')->toArray());

        // return '';
    }

    public function getSpvDemoAttribute(){
        return $this->spvDemo->user->name;
    }

    public function getSpvPromoterAttribute(){
        return $this->user->name;
    }

    public function getDmNameAttribute(){

        // return implode(', ', $this->district->area->dmAreas()->with('user')->get()->pluck('user.name')->toArray());

        return ($this->district->area->dmAreas()->count() > 0) ? $this->district->area->dmAreas()->first()->user->name : '';

    }

    public function getTrainerNameAttribute(){

        // return implode(', ', $this->district->area->trainerAreas()->with('user')->get()->pluck('user.name')->toArray());

        return ($this->district->area->trainerAreas()->count() > 0) ? $this->district->area->trainerAreas()->first()->user->name : '';

    }

    public function getRegionNameAttribute(){
        return ($this->attributes['district_id']) ? $this->district->area->region->name : '';
    }

    public function getAreaNameAttribute(){
        return ($this->attributes['district_id']) ? $this->district->area->name : '';
    }

    public function getDistrictNameAttribute(){
        return ($this->attributes['district_id']) ? $this->district->name : '';
    }

    public function getGlobalChannelNameAttribute(){
        return ($this->attributes['subchannel_id']) ? $this->subChannel->channel->globalChannel->name : '';
    }

    public function getChannelNameAttribute(){
        return ($this->attributes['subchannel_id']) ? $this->subChannel->channel->name : '';
    }

    public function getSubChannelNameAttribute(){
        return ($this->attributes['subchannel_id']) ? $this->subChannel->name : '';
    }

}
