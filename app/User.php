<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Filters\QueryFilters;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nik', 'name', 'email', 'password', 'role_id', 'status', 'photo', 'join_date', 'grading_id', 'certificate', 'status_login', 'jenis_hp', 'hp_id', 'fcm_token', 'is_resign', 'alasan_resign'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /* Metode tambahan untuk model Branch Sport. */

    /**
     * Relation Method(s).
     *
     */

    public function role()
    {
        return $this->belongsTo('App\Role', 'role_id');
    }

    public function grading()
    {
        return $this->belongsTo('App\Grading', 'grading_id');
    }
  
    public function spvDemos()
    {
        return $this->hasMany('App\SpvDemo', 'user_id');
    }

    public function salesmanDedicates()
    {
        return $this->hasMany('App\SalesmanDedicate', 'user_id');
    }

    public function rsmRegion()
    {
        return $this->hasOne('App\RsmRegion', 'user_id');
    }

    public function dmArea()
    {
        return $this->hasOne('App\DmArea', 'user_id');
    }

    public function trainerArea()
    {
        return $this->hasOne('App\TrainerArea', 'user_id');
    }

    public function employeeStores()
    {
        return $this->hasMany('App\EmployeeStore', 'user_id');
    }

    public function stores(){
        return $this->hasMany('App\Store', 'user_id');   
    }

    public function readNews(){
        return $this->hasMany('App\NewsRead', 'user_id');
    }

    public function readProductKnowledges(){
        return $this->hasMany('App\ProductKnowledgeRead', 'user_id');
    }

    public function newsAdmin(){
        return $this->hasMany('App\News', 'user_id');
    }

    public function productKnowledgeAdmin(){
        return $this->hasMany('App\ProductKnowledge', 'user_id');
    }

    public function messageToAdmin()
    {
        return $this->hasMany('App\MessageToAdmin', 'user_id');
    }
    public function feedbackAnswer()
    {
        return $this->morphToMany('App\FeedbackAnswer', 'user_id');
    }

    // Transactions

    public function sellInTransactions(){
        return $this->hasMany('App\SellIn', 'user_id');
    }

    public function sellOutTransactions(){
        return $this->hasMany('App\SellOut', 'user_id');
    }

    public function retDistributorTransactions(){
        return $this->hasMany('App\RetDitributor', 'user_id');
    }

    public function retConsumentTransactions(){
        return $this->hasMany('App\RetConsument', 'user_id');
    }

    public function freeProductTransactions(){
        return $this->hasMany('App\FreeProduct', 'user_id');
    }

    public function tbatTransactions(){
        return $this->hasMany('App\Tbat', 'user_id');
    }

    public function sohTransactions(){
        return $this->hasMany('App\SOH', 'user_id');
    }

    public function sosTransactions(){
        return $this->hasMany('App\Sos', 'user_id');
    }

    public function posmActivityTransactions(){
        return $this->hasMany('App\PosmActivity', 'user_id');
    }

    public function competitorActivityTransactions(){
        return $this->hasMany('App\CompetitorActivity', 'user_id');
    }

    public function promoActivityTransactions(){
        return $this->hasMany('App\PromoActivity', 'user_id');
    }

    public function attendance(){
        return $this->hasMany('App\Attendance', 'user_id');
    }    

    public function historyEmployeeStores()
    {
        return $this->hasMany('App\Reports\HistoryEmployeeStore', 'user_id');
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

    public function getDedicateAttribute(){
        if($this->attributes['role_id'] == null) return '';
        if($this->role->role_group == 'Salesman Explorer' || $this->role->role_group == 'SMD'){
            if($this->salesmanDedicates()->first()){
                if($this->salesmanDedicates()->first()->dedicate == 'Modern Retail') return 1;
                if($this->salesmanDedicates()->first()->dedicate == 'Traditional Retail') return 2;
                if($this->salesmanDedicates()->first()->dedicate == 'Mother Care & Child') return 3;
            }      
        }
        return '';
    }

}
