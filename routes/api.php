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
	Route::get('/group', 'Api\Master\GroupController@allGroup');
	Route::get('/product/{param}', 'Api\Master\ProductController@all');
	Route::get('/store', 'Api\Master\StoreController@all');
	Route::get('/store-promoter', 'Api\Master\StoreController@byPromoter');
	Route::get('/place', 'Api\Master\PlaceController@all');
	Route::get('/competitor/{param}', 'Api\Master\GroupCompetitorController@all');
	Route::get('/competitor/{param}/{param2}', 'Api\Master\GroupCompetitorController@allCategory');
	Route::get('/posm/{param}', 'Api\Master\PosmController@all');

	/**
     * User Module(s)
     */

	Route::get('/profile', 'Api\AuthController@getProfile');
	Route::post('/set-profile', 'Api\AuthController@setProfile');

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
	Route::get('/check-attendance', 'Api\Master\PromoterController@checkAttendance');
	Route::get('/check-not-attendance', 'Api\Master\PromoterController@checkNotAttendance');

	/**
     * Other(s)
     */

    Route::get('/news', 'Api\Master\NewsController@get');
    Route::get('/news/{param}', 'Api\Master\NewsController@read');

    Route::get('/guidelines/{param}', 'Api\Master\ProductKnowledgeController@get');
    Route::get('/guidelines-read/{param}', 'Api\Master\ProductKnowledgeController@read');

    /**
     * Supervisor Module(s)
     */

    Route::post('/promoter-attendance', 'Api\Master\PromoterController@getAttendanceForSupervisor');
    Route::post('/promoter-approval/{param}', 'Api\Master\PromoterController@approval');
    Route::get('/store-supervisor', 'Api\Master\StoreController@bySupervisor');
    Route::post('/store-update', 'Api\Master\StoreController@updateStore');

});
