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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


// GET USERS
Route::get('/users', 'UserController@index');

// CREATE USER
Route::post('/users', 'UserController@store');

// GET SERVICES
Route::get('/services', 'ServiceController@index');

// CREATE SERVICE
Route::post('/services', 'ServiceController@store');

// GET SUBSCRIPTIONS
Route::get('/subscriptions', 'SubscriptionController@index');

// CREATE SUBSCRIPTIONS
Route::post('/subscriptions', 'SubscriptionController@store');

// UPDATE SUBSCRIPTIONS -> UNSUSCRIBE
Route::put('/subscriptions/{subscriptionId}', 'SubscriptionController@update');

// GET REPORT SUBSCRIPTIONS
Route::get('/subscriptions/{date}', 'SubscriptionController@report');

// CREATE DATA TESTING
Route::get('/test', 'Controller@createData');
