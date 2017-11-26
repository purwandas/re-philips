<?php

/*
|--------------------------------------------------------------------------
| Master Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are related to master methods
|
*/

Route::group(['middleware' => ['auth']], function () {

    /**
     * Master Admin Module(s)
     */
    Route::group(['middleware' => ['master']], function () {

        /**
         * Master Module(s)
         */

        /** User (Include Employee) **/
        Route::get('user', 'UserController@index');
        Route::get('user/create', 'UserController@create');
        Route::post('user', 'UserController@store');
        Route::get('user/edit/{id}', 'UserController@edit');
        Route::patch('user/{id}', 'UserController@update');
        Route::delete('user/{id}', 'UserController@destroy');

        /** Area **/
        Route::get('area', 'Master\AreaController@index');
        Route::post('area', 'Master\AreaController@store');
        Route::get('area/edit/{id}', 'Master\AreaController@edit');
        Route::patch('area/{id}', 'Master\AreaController@update');
        Route::delete('area/{id}', 'Master\AreaController@destroy');

        /** AreaApp **/
        Route::get('areaapp', 'Master\AreaAppController@index');
        Route::post('areaapp', 'Master\AreaAppController@store');
        Route::get('areaapp/edit/{id}', 'Master\AreaAppController@edit');
        Route::patch('areaapp/{id}', 'Master\AreaAppController@update');
        Route::delete('areaapp/{id}', 'Master\AreaAppController@destroy');

        /** Account Type **/
        Route::get('accounttype', 'Master\AccountTypeController@index');        
        Route::post('accounttype', 'Master\AccountTypeController@store');
        Route::get('accounttype/edit/{id}', 'Master\AccountTypeController@edit');
        Route::patch('accounttype/{id}', 'Master\AccountTypeController@update');
        Route::delete('accounttype/{id}', 'Master\AccountTypeController@destroy');

        /** Account **/
        Route::get('account', 'Master\AccountController@index');        
        Route::post('account', 'Master\AccountController@store');
        Route::get('account/edit/{id}', 'Master\AccountController@edit');
        Route::patch('account/{id}', 'Master\AccountController@update');
        Route::delete('account/{id}', 'Master\AccountController@destroy');

        /** Store **/
        Route::get('store', 'Master\StoreController@index'); 
        Route::get('store/create', 'Master\StoreController@create');       
        Route::post('store', 'Master\StoreController@store');
        Route::get('store/edit/{id}', 'Master\StoreController@edit');
        Route::patch('store/{id}', 'Master\StoreController@update');
        Route::delete('store/{id}', 'Master\StoreController@destroy');

        /** Employee **/
        /*
        Route::get('employee', 'Master\EmployeeController@index');
        Route::get('employee/create', 'Master\EmployeeController@create');
        Route::post('employee', 'Master\EmployeeController@store');
        Route::get('employee/edit/{id}', 'Master\EmployeeController@edit');
        Route::patch('employee/{id}', 'Master\EmployeeController@update');
        Route::delete('employee/{id}', 'Master\EmployeeController@destroy');
        */

        /** Place **/
        Route::get('place', 'Master\PlaceController@index');        
        Route::post('place', 'Master\PlaceController@store');
        Route::get('place/edit/{id}', 'Master\PlaceController@edit');
        Route::patch('place/{id}', 'Master\PlaceController@update');
        Route::delete('place/{id}', 'Master\PlaceController@destroy');

        /** Group Competitor **/
        Route::get('groupcompetitor', 'Master\GroupCompetitorController@index');        
        Route::post('groupcompetitor', 'Master\GroupCompetitorController@store');
        Route::get('groupcompetitor/edit/{id}', 'Master\GroupCompetitorController@edit');
        Route::patch('groupcompetitor/{id}', 'Master\GroupCompetitorController@update');
        Route::delete('groupcompetitor/{id}', 'Master\GroupCompetitorController@destroy');

        /** Group **/
        Route::get('group', 'Master\GroupController@index');        
        Route::post('group', 'Master\GroupController@store');
        Route::get('group/edit/{id}', 'Master\GroupController@edit');
        Route::patch('group/{id}', 'Master\GroupController@update');
        Route::delete('group/{id}', 'Master\GroupController@destroy');

        /** Category **/
        Route::get('category', 'Master\CategoryController@index');        
        Route::post('category', 'Master\CategoryController@store');
        Route::get('category/edit/{id}', 'Master\CategoryController@edit');
        Route::patch('category/{id}', 'Master\CategoryController@update');
        Route::delete('category/{id}', 'Master\CategoryController@destroy');

        /** Product **/
        Route::get('product', 'Master\ProductController@index');        
        Route::post('product', 'Master\ProductController@store');
        Route::get('product/edit/{id}', 'Master\ProductController@edit');
        Route::patch('product/{id}', 'Master\ProductController@update');
        Route::delete('product/{id}', 'Master\ProductController@destroy');

        /** POS Material **/
        Route::get('posm', 'Master\PosmController@index');
        Route::post('posm', 'Master\PosmController@store');
        Route::get('posm/edit/{id}', 'Master\PosmController@edit');
        Route::patch('posm/{id}', 'Master\PosmController@update');
        Route::delete('posm/{id}', 'Master\PosmController@destroy');

    });

    /**
     * Master Admin & Admin Module(s)
     */

    Route::middleware(['admin'])->group(function () {

        /** Profile **/
        Route::get('profile', 'ProfileController@index');
        Route::post('profile', 'ProfileController@update');

        /** News **/
        Route::get('news', 'Master\NewsController@index');
        Route::get('news/create', 'Master\NewsController@create');
        Route::post('news', 'Master\NewsController@store');
        Route::get('news/edit/{id}', 'Master\NewsController@edit');
        Route::patch('news/{id}', 'Master\NewsController@update');
        Route::delete('news/{id}', 'Master\NewsController@destroy');

        /** Product Knowledge **/
        Route::get('product-knowledge', 'Master\ProductKnowledgeController@index');
        Route::get('product-knowledge/create', 'Master\ProductKnowledgeController@create');
        Route::post('product-knowledge', 'Master\ProductKnowledgeController@store');
        Route::get('product-knowledge/edit/{id}', 'Master\ProductKnowledgeController@edit');
        Route::patch('product-knowledge/{id}', 'Master\ProductKnowledgeController@update');
        Route::delete('product-knowledge/{id}', 'Master\ProductKnowledgeController@destroy');

        


    });

    /**
     * Reporting Module(s)
     */

    Route::get('posmactivityreport', 'Master\ReportController@posmActivityIndex');
    Route::get('sellinreport', 'Master\ReportController@sellInIndex');
    Route::get('selloutreport', 'Master\ReportController@sellOutIndex');
    Route::get('retconsumentreport', 'Master\ReportController@retConsumentIndex');
    Route::get('retdistributorreport', 'Master\ReportController@retDistributorIndex');
    Route::get('tbatreport', 'Master\ReportController@tbatIndex');
    Route::get('sohreport', 'Master\ReportController@sohIndex');
    Route::get('sosreport', 'Master\ReportController@sosIndex');
        
});


