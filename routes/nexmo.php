<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//simple hello world
Route::get('/answer', function (Request $request) {
    return [
        [
            'action' => 'talk',
            'text' => 'Welcome to the Laravel Hotline'
        ]

    ];
});

//just log the events so we can inspect the data
Route::post('/event', function (Request $request) {
    error_log($request->getContent());
    return;
});
