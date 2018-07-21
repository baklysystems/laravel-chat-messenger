<?php

    /**
     * Laravel messenger routes.
     */

    Route::prefix('messenger')->group(function () {
        Route::get('t/{id}', 'App\Http\Controllers\MessageController@laravelMessenger')->name('messenger');
        Route::post('send', 'App\Http\Controllers\MessageController@store')->name('message.store');
        Route::get('threads', 'App\Http\Controllers\MessageController@loadThreads')->name('threads');
        Route::get('more/messages', 'App\Http\Controllers\MessageController@moreMessages')->name('more.messages');
        Route::delete('delete/{id}', 'App\Http\Controllers\MessageController@destroy')->name('delete');
        // AJAX requests.
        Route::prefix('ajax')->group(function () {
            Route::post('make-seen', 'App\Http\Controllers\MessageController@makeSeen')->name('make-seen');
        });
    });
