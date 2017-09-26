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

    /**
     * Data with filter (select2, list)
     */

	Route::post('data/region', ['as'=> 'data.region','uses'=>'Master\RegionController@getDataWithFilters']);
	Route::post('data/area', ['as'=> 'data.area','uses'=>'Master\AreaController@getDataWithFilters']);
    Route::post('data/accounttype', ['as'=> 'data.accounttype','uses'=>'Master\AccountTypeController@getDataWithFilters']);

    /**
     * Relation
     */

    Route::post('relation/areaareaapps', ['as'=> 'relation.areaareaapps','uses'=>'RelationController@areaAreaAppsRelation']);
    Route::post('relation/areadm', ['as'=> 'relation.areadm','uses'=>'RelationController@areaDmRelation']);
    Route::post('relation/accounttypeaccount', ['as'=> 'relation.accounttypeaccount','uses'=>'RelationController@accountTypeAccountRelation']);

    /**
     * Util
     */

    Route::post('util/existemail', ['as'=> 'util.existemail','uses'=>'UtilController@existEmail']);
    
});


