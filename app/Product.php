<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'model', 'name'
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

    public function category()
    {
        return $this->belongsTo('App\Category', 'category_id');
    }

    // Transactions

    public function sellInDetails(){
        return $this->hasMany('App\SellInDetail', 'product_id');
    }

    public function sellOutDetails(){
        return $this->hasMany('App\SellOutDetail', 'product_id');
    }

    public function retDistributorDetails(){
        return $this->hasMany('App\RetDitributorDetail', 'product_id');
    }

    public function retConsumentDetails(){
        return $this->hasMany('App\RetConsumentDetail', 'product_id');
    }

    public function freeProductDetails(){
        return $this->hasMany('App\FreeProductDetail', 'product_id');
    }

    public function tbatDetails(){
        return $this->hasMany('App\TbatDetail', 'product_id');
    }

    public function sohDetails(){
        return $this->hasMany('App\SOHDetail', 'product_id');
    }

    public function sosDetails(){
        return $this->hasMany('App\SosDetail', 'product_id');
    }

    public function promoDetails(){
        return $this->hasMany('App\PromoActivityDetail', 'product_id');
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
