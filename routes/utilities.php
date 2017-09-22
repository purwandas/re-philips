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

    /**
     * Util
     */

    Route::post('util/existemail', ['as'=> 'util.existemail','uses'=>'UtilController@existEmail']);
    
});


