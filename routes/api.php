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

Route::get('athletes', 'Api\AthleteControllers@index');
Route::get('countries', 'Api\CountriesController@index');
Route::get('branch_sports', 'Api\BranchSportController@index');
Route::get('tour', 'Api\TourController@index');
Route::post('nearby_competition', 'Api\BranchSportController@nearby_competition');
Route::post('nearby_tour', 'Api\TourController@nearby_competition');

/* JWT Authentication */

Route::post('auth/login', 'Api\AuthController@authenticate');

/* End point module(s) */

Route::group(['middleware' => 'jwt.auth'], function () {
	  
	Route::get('/getUser', 'Api\AuthController@getAuthenticatedUser');

});
