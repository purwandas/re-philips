<?php

/*
|--------------------------------------------------------------------------
| Utilities Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are related to utilities methods
|
*/

Route::group(['middleware' => ['auth']], function () {

    /**
     * Datatable
     */

    Route::post('datatable/user', ['as'=> 'datatable.user','uses'=>'UserController@masterDataTable']);
    Route::post('datatable/area', ['as'=> 'datatable.area','uses'=>'Master\AreaController@masterDataTable']);
    Route::post('datatable/areaapp', ['as'=> 'datatable.areaapp','uses'=>'Master\AreaAppController@masterDataTable']);
    Route::post('datatable/accounttype', ['as'=> 'datatable.accounttype','uses'=>'Master\AccountTypeController@masterDataTable']);
    Route::post('datatable/account', ['as'=> 'datatable.account','uses'=>'Master\AccountController@masterDataTable']);
    Route::post('datatable/employee', ['as'=> 'datatable.employee','uses'=>'Master\EmployeeController@masterDataTable']);
    Route::post('datatable/store', ['as'=> 'datatable.store','uses'=>'Master\StoreController@masterDataTable']);
    Route::post('datatable/place', ['as'=> 'datatable.place','uses'=>'Master\PlaceController@masterDataTable']);
    Route::post('datatable/groupcompetitor', ['as'=> 'datatable.groupcompetitor','uses'=>'Master\GroupCompetitorController@masterDataTable']);

    /**
     * Data with filter (select2, list)
     */

	Route::post('data/region', ['as'=> 'data.region','uses'=>'Master\RegionController@getDataWithFilters']);
	Route::post('data/area', ['as'=> 'data.area','uses'=>'Master\AreaController@getDataWithFilters']);
    Route::post('data/accounttype', ['as'=> 'data.accounttype','uses'=>'Master\AccountTypeController@getDataWithFilters']);
    Route::post('data/account', ['as'=> 'data.account','uses'=>'Master\AccountController@getDataWithFilters']);
    Route::post('data/areaapp', ['as'=> 'data.areaapp','uses'=>'Master\AreaAppController@getDataWithFilters']);
    Route::post('data/employee', ['as'=> 'data.employee','uses'=>'Master\EmployeeController@getDataWithFilters']);
    Route::post('data/store', ['as'=> 'data.store','uses'=>'Master\StoreController@getDataWithFilters']);
    Route::post('data/groupproduct', ['as'=> 'data.groupproduct','uses'=>'Master\GroupProductController@getDataWithFilters']);

    /**
     * Relation
     */

    Route::post('relation/areaareaapps', ['as'=> 'relation.areaareaapps','uses'=>'RelationController@areaAreaAppsRelation']);
    Route::post('relation/areadm', ['as'=> 'relation.areadm','uses'=>'RelationController@areaDmRelation']);
    Route::post('relation/accounttypeaccount', ['as'=> 'relation.accounttypeaccount','uses'=>'RelationController@accountTypeAccountRelation']);
    Route::post('relation/storeaccount', ['as'=> 'relation.storeaccount','uses'=>'RelationController@storeAccountRelation']);
    Route::post('relation/areaapp', ['as'=> 'relation.areaapp','uses'=>'RelationController@storeAreaAppRelation']);
    Route::post('relation/storespv', ['as'=> 'relation.storespv','uses'=>'RelationController@storeSpvRelation']);

    /**
     * Util
     */

    Route::post('util/existemailuser', ['uses'=>'UtilController@existEmailUser']);    
    Route::post('util/existemailemployee', ['uses'=>'UtilController@existEmailEmployee']);
    Route::get('util/empstore/{id}', ['uses'=>'UtilController@getStoreForEmployee']);
    
});


