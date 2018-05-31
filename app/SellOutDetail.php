<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class SellOutDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sellout_id', 'product_id', 'quantity', 'irisan', 'price'
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

    public function sellOut()
    {
        return $this->belongsTo('App\SellOut', 'sellout_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
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

    public function getAmount2Attribute(){

        // $price = 0;

        // if($this->sellOut->user->role->role_group == 'Salesman Explorer' || $this->sellOut->user->role->role_group == 'SMD'){

        //     if($this->sellOut->store->globalChannelId == ''){

        //         $price = $this->product->price->where('globalchannel_id', $this->user->dedicate)->first()->price;

        //         return $this->attributes['quantity'] * $price;

        //     }

        // }

        // if($this->sellOut->store->globalChannelId != ''){

        //     $price = $this->product->price->where('globalchannel_id', $this->sellOut->store->globalChannelId)->first()->price;

        //     return $this->attributes['quantity'] * $price;

        // }

        // return $this->attributes['quantity'] * $price;

        // return $this->attributes['quantity'] * $this->getPriceAttribute();

        return $this->attributes['quantity'] * $this->attributes['amount'];

    }

    public function getPriceAttribute(){

        $price = 2;

        // if($this->sellOut->user->role->role_group == 'Salesman Explorer' || $this->sellOut->user->role->role_group == 'SMD'){

        //     if($this->sellOut->store->globalChannelId == ''){

        //         if($this->sellOut->user->dedicate != ''){

        //             $cekPrice = $this->product->price->where('globalchannel_id', $this->sellOut->user->dedicate)->first();

        //             if($cekPrice){
        //                 return $cekPrice->price;
        //             }    

        //         }

        //     }

        // }

        // if($this->sellOut->store->globalChannelId != ''){

        //     $cekPrice = $this->product->price->where('globalchannel_id', $this->sellOut->store->globalChannelId)->first();

        //     if($cekPrice){
        //         return $cekPrice->price;
        //     }

        // }

        return $price;

    }

    // public function getWeekAttribute(){
    //     return $this->sellOut->week;
    // }

    public function getTestAttribute(){
        return 'AAA';
    }

    // MAIN 

    public function getDistributorCodeAttribute(){
        // return $this->sellOut->id;
        return '';
    }

    public function getDistributorNameAttribute(){
        return $this->attributes['id'];
    }

}
