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
    Route::post('datatable/news', ['as'=> 'datatable.news','uses'=>'Master\NewsController@masterDataTable']);
    Route::post('datatable/productknowledge', ['as'=> 'datatable.productknowledge','uses'=>'Master\ProductKnowledgeController@masterDataTable']);
    Route::post('datatable/posm', ['as'=> 'datatable.posm','uses'=>'Master\PosmController@masterDataTable']);
    Route::post('datatable/soh', ['as'=> 'datatable.soh','uses'=>'Master\SohController@masterDataTable']);
    Route::post('datatable/sellinreport', ['as'=> 'datatable.sellinreport','uses'=>'Master\ReportController@sellInData']);

    /**
     * Data with filter (select2, list)
     */

	Route::post('data/region', ['as'=> 'data.region','uses'=>'Master\RegionController@getDataWithFilters']);
	Route::post('data/area', ['as'=> 'data.area','uses'=>'Master\AreaController@getDataWithFilters']);
    Route::post('data/accounttype', ['as'=> 'data.accounttype','uses'=>'Master\AccountTypeController@getDataWithFilters']);
    Route::post('data/account', ['as'=> 'data.account','uses'=>'Master\AccountController@getDataWithFilters']);
    Route::post('data/areaapp', ['as'=> 'data.areaapp','uses'=>'Master\AreaAppController@getDataWithFilters']);
    Route::post('data/employee', ['as'=> 'data.employee','uses'=>'UserController@getDataWithFilters']);
    Route::post('data/promoter', ['as'=> 'data.promoter','uses'=>'UserController@getDataPromoterWithFilters']);
    Route::post('data/store', ['as'=> 'data.store','uses'=>'Master\StoreController@getDataWithFilters']);
    
    Route::post('data/groupproduct', ['as'=> 'data.groupproduct','uses'=>'Master\GroupProductController@getDataWithFilters']);
    Route::post('data/group', ['as'=> 'data.group','uses'=>'Master\GroupController@getDataWithFilters']);
    Route::post('data/category', ['as'=> 'data.category','uses'=>'Master\CategoryController@getDataWithFilters']);

    /**
     * Relation
     */

    // Route::post('relation/areaaappsarea', ['as'=> 'relation.areaaappsarea','uses'=>'RelationController@areaAppsAreaRelation']);    
    // Route::post('relation/accountaccounttype', ['as'=> 'relation.accountaccounttype','uses'=>'RelationController@accountAccountTypeRelation']);
    // Route::post('relation/storeaccount', ['as'=> 'relation.storeaccount','uses'=>'RelationController@storeAccountRelation']);
    // Route::post('relation/areaapp', ['as'=> 'relation.areaapp','uses'=>'RelationController@storeAreaAppRelation']);
    // Route::post('relation/storespv', ['as'=> 'relation.storespv','uses'=>'RelationController@storeSpvRelation']);
    // Route::post('relation/salesemployee', ['as'=> 'relation.salesemployee','uses'=>'RelationController@salesEmployeeRelation']);
    // Route::post('relation/salesstore', ['as'=> 'relation.salesstore','uses'=>'RelationController@salesStoreRelation']);
    // Route::post('relation/categorygroup', ['as'=> 'relation.categorygroup','uses'=>'RelationController@categoryGroupRelation']);
    // Route::post('relation/productcategory', ['as'=> 'relation.productcategory','uses'=>'RelationController@productCategoryRelation']);
    Route::post('relation/storespvchange', ['as'=> 'relation.storespvchange','uses'=>'RelationController@storeSpvChangeRelation']);
    Route::post('relation/salesemployeechange', ['as'=> 'relation.salesemployeechange','uses'=>'RelationController@salesEmployeeChangeRelation']);
    // Route::post('relation/newsemployee', ['as'=> 'relation.newsemployee','uses'=>'RelationController@newsEmployeeRelation']);
    // Route::post('relation/newsstore', ['as'=> 'relation.newsstore','uses'=>'RelationController@newsStoreRelation']);
    // Route::post('relation/newsarea', ['as'=> 'relation.newsarea','uses'=>'RelationController@newsAreaRelation']);
    // Route::post('relation/posmactivitydetailposm', ['as'=> 'relation.posmactivitydetailposm','uses'=>'RelationController@posmActivityDetailPosmRelation']);
    // Route::post('relation/posmactivityemployee', ['as'=> 'relation.posmactivityemployee','uses'=>'RelationController@posmActivityEmployeeRelation']);
    // Route::post('relation/posmactivitystore', ['as'=> 'relation.posmactivitystore','uses'=>'RelationController@posmActivityStoreRelation']);
    // Route::post('relation/newsadmin', ['as'=> 'relation.newsadmin','uses'=>'RelationController@newsAdminRelation']);
    // Route::post('relation/productknowledgeemployee', ['as'=> 'relation.productknowledgeemployee','uses'=>'RelationController@productKnowledgeEmployeeRelation']);
    // Route::post('relation/productknowledgestore', ['as'=> 'relation.productknowledgestore','uses'=>'RelationController@productKnowledgeStoreRelation']);
    Route::post('relation/productknowledgearea', ['as'=> 'relation.productknowledgearea','uses'=>'RelationController@productKnowledgeAreaRelation']);
    // Route::post('relation/productknowledgeadmin', ['as'=> 'relation.productknowledgeadmin','uses'=>'RelationController@productKnowledgeAdminRelation']);
    // Route::post('relation/competitoractivitygroup', ['as'=> 'relation.competitoractivitygroup','uses'=>'RelationController@competitorActivityGroupRelation']);
    // Route::post('relation/salesproduction', ['as'=> 'relation.salesproduction','uses'=>'RelationController@salesProductRelation']);
    Route::post('relation/userrelation', ['as'=> 'relation.userrelation','uses'=>'RelationController@checkUserRelation']);
    Route::post('relation/storerelation', ['as'=> 'relation.storerelation','uses'=>'RelationController@checkStoreRelation']);
    Route::post('relation/productionrelation', ['as'=> 'relation.productionrelation','uses'=>'RelationController@checkProductRelation']);
    Route::post('relation/posmrelation', ['as'=> 'relation.posmrelation','uses'=>'RelationController@checkPosmRelation']);
    Route::post('relation/groupcompetitorrelation', ['as'=> 'relation.groupcompetitor','uses'=>'RelationController@checkGroupCompetitorRelation']);
    Route::post('relation/grouprelation', ['as'=> 'relation.grouprelation','uses'=>'RelationController@checkGroupRelation']);
    Route::post('relation/employeerelation', ['as'=> 'relation.employeerelation','uses'=>'RelationController@checkEmployeeRelation']);
    Route::post('relation/categoryrelation', ['as'=> 'relation.categoryrelation','uses'=>'RelationController@checkCategoryRelation']);
    Route::post('relation/areaapprelation', ['as'=> 'relation.areaapprelation','uses'=>'RelationController@CheckAreaAppRelation']);
    Route::post('relation/arearelation', ['as'=> 'relation.arearelation','uses'=>'RelationController@checkAreaRelation']);
    Route::post('relation/accounttyperelation', ['as'=> 'relation.accountaccounttype','uses'=>'RelationController@checkAccountTypeRelation']);
    Route::post('relation/accountrelation', ['as'=> 'relation.accountrelation','uses'=>'RelationController@checkAccountRelation']);    
    

    /**
     * Util
     */

    Route::post('util/existemailuser', ['uses'=>'UtilController@existEmailUser']);    
    Route::post('util/existemailemployee', ['uses'=>'UtilController@existEmailEmployee']);
    Route::get('util/empstore/{id}', ['uses'=>'UtilController@getStoreForEmployee']);
    Route::get('util/areaapp/{id}', ['uses'=>'UtilController@getAreaApp']);
    Route::get('util/store/{id}', ['uses'=>'UtilController@getStore']);
    Route::get('util/user/{id}', ['uses'=>'UtilController@getUser']);
    Route::get('util/newsread/{id}', ['uses'=>'UtilController@getNewsRead']);
    Route::get('util/productread/{id}', ['uses'=>'UtilController@getProductRead']);
    
});


