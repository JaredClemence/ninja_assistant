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
    Route::get('/contacts/info', function(){ 
        return view('contacts.upload.start');
    } )->name('upload_contacts_instruction');
    Route::get('/contacts/android', function(){ 
        return view('contacts.upload.android');
    } )->name('android_instruction');
    Route::get('/contacts/iphone', function(){ 
        return "iPhone";
    } )->name('iphone_instruction');
    Route::get('/contacts/upload', function(){ 
        return view('contacts.upload.upload');
    } )->name('upload_csv');
    Route::post('/contacts/upload', 'ContactCsvFileController@upload');
    Route::get('/contacts/success', function(){
        return "Successful file upload.";
    });
});
