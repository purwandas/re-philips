<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* No Authentication Route */

Route::get('tes', 'Api\AuthController@tes');

/* JWT Authentication */

Route::post('login', 'Api\AuthController@login');

/* End point module(s) */

Route::group(['middleware' => 'jwt.auth'], function () {
	  
	Route::get('/user', 'Api\AuthController@getUser');

});
