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

    Route::group(['middleware' => ['master']], function () {

        /**
         * Master Module(s)
         */

        /** User **/
        Route::get('user', 'UserController@index');
        Route::get('user/create', 'UserController@create');
        Route::post('user', 'UserController@store');
        Route::get('user/edit/{id}', 'UserController@edit');
        Route::patch('user/{id}', 'UserController@update');
        Route::delete('user/{id}', 'UserController@destroy');

        /** Profile **/
        Route::get('profile', 'ProfileController@index');
        Route::post('profile', 'ProfileController@update');

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
        Route::get('employee', 'Master\EmployeeController@index');
        Route::get('employee/create', 'Master\EmployeeController@create');
        Route::post('employee', 'Master\EmployeeController@store');
        Route::get('employee/edit/{id}', 'Master\EmployeeController@edit');
        Route::patch('employee/{id}', 'Master\EmployeeController@update');
        Route::delete('employee/{id}', 'Master\EmployeeController@destroy');

        /** Place **/
        Route::get('place', 'Master\PlaceController@index');        
        Route::post('place', 'Master\PlaceController@store');
        Route::get('place/edit/{id}', 'Master\PlaceController@edit');
        Route::patch('place/{id}', 'Master\PlaceController@update');
        Route::delete('place/{id}', 'Master\PlaceController@destroy');

    });
        
});


