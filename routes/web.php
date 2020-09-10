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
	    return redirect('/ninja/daily');
	});
    Route::get('/ninja/daily', 'Ninja\NinjaController@showDaily')->name('daily_call');
    Route::get('/ninja/force', 'Ninja\NinjaController@refreshDaily')->name('refresh_daily');
    Route::get('/ninja/{contact}/skip', 'Ninja\NinjaController@skipContact')->name('skip_contact');
    Route::get('/ninja/{contact}/deactivate', 'Ninja\NinjaController@deactivateContact')->name('deactivate_contact');
    Route::get('/contacts/{contact}/edit', 'ContactController@edit')->name('edit_contact');
    Route::post('/contacts/{contact}/update', 'ContactController@update')->name('update_contact');
    Route::get('/contacts/{contact}/show', 'ContactController@show')->name('contact.show');
    Route::get('/contacts/all', 'ContactController@index')->name('index_contacts');
    Route::get('/contacts/{contact}/delete', 'ContactController@destroy')->name('delete_contact');
    Route::get('/ninja/{contact}/activity/{action}/create', 'Ninja\DailyActivityLogController@create')->name('create_activity_log');
    Route::get('/ninja/{contact}/activity/{log}/edit', 'Ninja\DailyActivityLogController@edit')->name('edit_activity_log');
    Route::post('/ninja/{contact}/activity/{log}/edit', 'Ninja\DailyActivityLogController@update');
    Route::get('/ninja/activity/log', 'Ninja\DailyActivityLogController@index' )->name('activity.list');
    Route::get('/ninja/activity/{activity}','Ninja\DailyActivityLogController@show' )->name('activity.view');
} );
Route::group(['middleware'=>['auth']], function(){
    Route::get('/contacts/new', 'ContactController@create')->name('create_contact');
    Route::get('/contacts/info', function(){ 
        return view('contacts.upload.start');
    } )->name('upload_contacts_instruction');
    Route::get('/contacts/android', function(){ 
        return view('contacts.upload.android');
    } )->name('android_instruction');
    Route::get('/contacts/iphone', function(){ 
        return view('contacts.upload.iphone');
    } )->name('iphone_instruction');
    Route::get('/contacts/upload', 'ContactCsvFileController@uploadForm' )->name('upload_csv');
    Route::post('/contacts/upload', 'ContactCsvFileController@upload');
    Route::get('/contacts/verify', 'ContactCsvFileController@showIntermediaries')->name('contact_preview');
    Route::get('/contacts/success', function(){
        return view('contacts.upload.success');
    });
    Route::get('/intermediate/{id}/delete','ContactCsvFileController@deleteIntermediaries');
    Route::get('/intermediates/approve','ContactCsvFileController@approveIntermediateRecords');
    Route::get('/intermediates/success', function(){
        return view('contacts.upload.intermediate_success');
    });
});
