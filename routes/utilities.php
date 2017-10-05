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
    Route::post('datatable/group', ['as'=> 'datatable.group','uses'=>'Master\GroupController@masterDataTable']);
    Route::post('datatable/category', ['as'=> 'datatable.category','uses'=>'Master\CategoryController@masterDataTable']);
    Route::post('datatable/product', ['as'=> 'datatable.product','uses'=>'Master\ProductController@masterDataTable']);

    /**
     * Data with filter (select2, list)
     */

	Route::post('data/region', ['as'=> 'data.region','uses'=>'Master\RegionController@getDataWithFilters']);
	Route::post('data/area', ['as'=> 'data.area','uses'=>'Master\AreaController@getDataWithFilters']);
    Route::post('data/accounttype', ['as'=> 'data.accounttype','uses'=>'Master\AccountTypeController@getDataWithFilters']);
    Route::post('data/account', ['as'=> 'data.account','uses'=>'Master\AccountController@getDataWithFilters']);
    Route::post('data/areaapp', ['as'=> 'data.areaapp','uses'=>'Master\AreaAppController@getDataWithFilters']);
    Route::post('data/employee', ['as'=> 'data.employee','uses'=>'UserController@getDataWithFilters']);
    Route::post('data/store', ['as'=> 'data.store','uses'=>'Master\StoreController@getDataWithFilters']);
    Route::post('data/groupproduct', ['as'=> 'data.groupproduct','uses'=>'Master\GroupProductController@getDataWithFilters']);
    Route::post('data/group', ['as'=> 'data.group','uses'=>'Master\GroupController@getDataWithFilters']);
    Route::post('data/category', ['as'=> 'data.category','uses'=>'Master\CategoryController@getDataWithFilters']);

    /**
     * Relation
     */

    Route::post('relation/areaaappsarea', ['as'=> 'relation.areaaappsarea','uses'=>'RelationController@areaAppsAreaRelation']);    
    Route::post('relation/accountaccounttype', ['as'=> 'relation.accountaccounttype','uses'=>'RelationController@accountAccountTypeRelation']);
    Route::post('relation/storeaccount', ['as'=> 'relation.storeaccount','uses'=>'RelationController@storeAccountRelation']);
    Route::post('relation/areaapp', ['as'=> 'relation.areaapp','uses'=>'RelationController@storeAreaAppRelation']);
    Route::post('relation/storespv', ['as'=> 'relation.storespv','uses'=>'RelationController@storeSpvRelation']);
    Route::post('relation/salesemployee', ['as'=> 'relation.salesemployee','uses'=>'RelationController@salesEmployeeRelation']);        
    Route::post('relation/categorygroup', ['as'=> 'relation.categorygroup','uses'=>'RelationController@categoryGroupRelation']);
    Route::post('relation/productcategory', ['as'=> 'relation.productcategory','uses'=>'RelationController@productCategoryRelation']);
    Route::post('relation/storespvchange', ['as'=> 'relation.storespvchange','uses'=>'RelationController@storeSpvChangeRelation']);
    Route::post('relation/salesemployeechange', ['as'=> 'relation.salesemployeechange','uses'=>'RelationController@salesEmployeeChangeRelation']);

    /**
     * Util
     */

    Route::post('util/existemailuser', ['uses'=>'UtilController@existEmailUser']);    
    Route::post('util/existemailemployee', ['uses'=>'UtilController@existEmailEmployee']);
    Route::get('util/empstore/{id}', ['uses'=>'UtilController@getStoreForEmployee']);
    
});


