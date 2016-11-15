<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('login', 'Auth\LoginSSOController@login');
Route::post('login', 'Auth\LoginSSOController@login');

Route::get('auth/google', 'Auth\AuthController@redirectToGoogle')->middleware('checkgoogleauth');
Route::get('auth/google/callback', 'Auth\AuthController@handleGoogleCallback');
Route::get('auth/logout', 'Auth\AuthController@logout')->middleware('checkgoogleauth')->name('logout');

/*
|--------------------------------------------------------------------------
| Register Routes Pattern
|--------------------------------------------------------------------------
*/
Route::pattern('home', '(?i)home(?-i)');
Route::pattern('newpost', '(?i)newpost(?-i)');

/*
|--------------------------------------------------------------------------
| Main Routes Function
|--------------------------------------------------------------------------
*/
Route::get('/', 'HomeController@index')->middleware('checkgoogleauth')->name('dashboard');
Route::get('+/{home}', 'PlusDomainController@index')->middleware('checkgoogleauth')->name('plustimeline');
Route::get('+/{newpost}', 'PlusDomainController@newPostLoad')->middleware('checkgoogleauth');
Route::post('+/{newpost}', 'PlusDomainController@newPost')->middleware('checkgoogleauth');