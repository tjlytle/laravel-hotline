<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get ('/answer', 'IvrController@answer')->name('ivr.answer');
Route::post('/menu',   'IvrController@menu'  )->name('ivr.menu');

//just log the events so we can inspect the data
Route::post('/event', function (Request $request) {
    error_log($request->getContent());
    return;
});
