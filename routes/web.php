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

//Route::get('/', function () {
//    return view('home');
//});

Route::get('/', "HomeController@index");
Auth::routes();

Route::get('/home', 'HomeController@index');

Route::get('/inquiry/received', 'InquiryController@received');
Route::get('/inquiry/reply/{inquiry_id}', 'InquiryController@reply');
Route::get('/inquiry/details/{inquiry_id}', 'InquiryController@details');
Route::post('/inquiry/update', 'InquiryController@update');

Route::resource('profile', 'ProfileController');

Route::resource('inquiry', 'InquiryController');

//Route::post('/profile/{$id}/update', 'ProfileController@update');