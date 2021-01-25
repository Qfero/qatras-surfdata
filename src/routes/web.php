<?php

use Illuminate\Support\Facades\Route;


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

// Route::get('visittrack', VisitTrackController::class, 'track')->name('visittrack.index');
// Route::post('visittrack', VisitTrackController::class, 'store')->name('visittrack.store');


Route::group(['namespace' => 'Qatras\Surfdata\Http\Controllers'], function(){

    Route::get('displaytrackdata', 'VisitTrackController@display')->name('displaytrackdata');
    Route::post('contact', 'FormController@process')->name('contact');

});



