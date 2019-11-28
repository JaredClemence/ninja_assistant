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


Auth::routes();

Route::group(['middleware'=>['auth','has_contacts']], function(){
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/', function () {
	    return view('welcome');
	});
    Route::get('/ninja/daily', function()
        { return "Daily Call List"; })->name('daily_call');
} );
Route::group(['middleware'=>['auth']], function(){
    Route::get('/contacts/info', function(){ return "Instructoins"; } )->name('upload_contacts_instruction');
});
