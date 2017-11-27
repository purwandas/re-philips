<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view('auth.login');
});

Route::get('/tes', 'ProfileController@sellin');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', 'DashboardController@index');
});

/* Route for Authentication (Login) */
Auth::routes();

/* Fix method jika user logout lewat url */
Route::get('logout', 'Auth\LoginController@logout');

/* Buat batasin akses register, dan reset password */
Route::match(['get', 'post'], 'register', function(){
    return redirect('/');
});

Route::match(['get', 'post'], 'password/reset', function(){
    return redirect('/');
});

Route::match(['get', 'post'], 'password/email', function(){
    return redirect('/');
});

/* Additional Method(s) */

/* Method untuk daftar admin ketika aplikasi first run */
Route::get('createadmin', 'Auth\OnceController@createAdmin');

/* Method untuk generate master */
Route::get('createmaster', 'Auth\OnceController@createMaster');

Route::get('geo', 'Auth\OnceController@tesGeo');