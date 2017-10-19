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

	/**
     * Master Module(s)
     */

	Route::get('/group/{param}', 'Api\Master\GroupController@all');
	Route::get('/product/{param}', 'Api\Master\ProductController@all');
	Route::get('/store', 'Api\Master\StoreController@all');
	Route::get('/place', 'Api\Master\PlaceController@all');
	Route::get('/competitor/{param}', 'Api\Master\GroupCompetitorController@all');
	Route::get('/competitor/{param}/{param2}', 'Api\Master\GroupCompetitorController@allCategory');
	Route::get('/posm/{param}', 'Api\Master\PosmController@all');

	/**
     * Transaction Module(s)
     */

	Route::post('/sales/{param}', 'Api\Master\SalesController@store');
	Route::post('/posm', 'Api\Master\PosmController@store');
	Route::post('/soh', 'Api\Master\SOHController@store');
	Route::post('/sos', 'Api\Master\SOSController@store');
	Route::post('/competitoractivity', 'Api\Master\CompetitorActivityController@store');
	Route::post('/promoactivity', 'Api\Master\PromoActivityController@store');

	/**
     * Attendance Module(s)
     */

	Route::post('/attendance/{param}', 'Api\Master\AttendanceController@attendance');
	Route::post('/store-nearby', 'Api\Master\StoreController@nearby');
	Route::post('/place-nearby', 'Api\Master\PlaceController@nearby');

	/**
     * Other(s)
     */

    Route::get('/news', 'Api\Master\NewsController@get');
    Route::get('/news/{param}', 'Api\Master\NewsController@read');

    Route::get('/product-knowledge', 'Api\Master\ProductKnowledgeController@get');
    Route::get('/product-knowledge/{param}', 'Api\Master\ProductKnowledgeController@read');

});
