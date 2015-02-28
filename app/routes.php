<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

/*
* ----------------------------------
*  USER ROUTES
* ----------------------------------
*/
Route::get('user/{id}', [
	'uses' => 'IndexController@user'
]);

Route::post('user/register', [
	'uses' => 'IndexController@register'
]);

Route::post('user/points/{id}', [
	'uses' => 'IndexController@addPoints'
]);

Route::get('user/selfie/{id}', [
	'uses' => 'IndexController@getSelfie'
]);

Route::post('user/selfie/{id}', [
	'uses' => 'IndexController@uploadSelfie'
]);

/*
* ----------------------------------
*  USERS ROUTES
* ----------------------------------
*/

Route::get('users', [
	'uses' => 'IndexController@users'
]);

Route::get('users/leaderboard/{fieldaction}', [
	'uses' => 'IndexController@leaderBoard'
]);

Route::get('users/selfie', [
	'uses' => 'IndexController@getAllSelfies'
]);
