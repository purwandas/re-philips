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
     * Master Module(s)
     */

    /** User **/
    Route::get('user', 'UserController@index');
    Route::get('user/create', 'UserController@create');
    Route::post('user', 'UserController@store');
    Route::get('user/edit/{id}', 'UserController@edit');
    Route::patch('user/{id}', 'UserController@update');
    Route::delete('user/{id}', 'UserController@destroy');
        
});


