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
Route::post('/home/contactus', 'HomeController@contactus');
Route::get('/home/faq', 'HomeController@faq');

Route::get('/inquiry/received', 'InquiryController@received');
Route::get('/inquiry/supplier/{inquiry_id}', 'InquiryController@supplier');
Route::get('/inquiry/suppliers', 'InquiryController@suppliers');
Route::get('/inquiry/shortView/{inquiry_id}', 'InquiryController@shortView');
Route::get('/inquiry/reply/{inquiry_id}', 'InquiryController@reply');
Route::get('/inquiry/closeSellerInquiry/{inquiry_id}', 'InquiryController@closeSellerInquiry');
Route::get('/inquiry/deleteSellerInquiry/{inquiry_id}', 'InquiryController@deleteSellerInquiry');
Route::get('/inquiry/closeInquiry/{inquiry_id}', 'InquiryController@closeInquiry');
Route::get('/inquiry/deleteInquiry/{inquiry_id}', 'InquiryController@deleteInquiry');
Route::get('/inquiry/details/{inquiry_id}', 'InquiryController@details');
Route::post('/inquiry/update', 'InquiryController@update');
Route::post('/auth/register', 'RegisterController@register');

Route::get('/users/verify/{confirmation_code}', 'UsersController@confirm');

Route::resource('profile', 'ProfileController');

Route::resource('inquiry', 'InquiryController');



//Route::post('/profile/{$id}/update', 'ProfileController@update');