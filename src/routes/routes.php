<?php

    /*
     * Laravel messenger routes.
     */

    Route::resource('messages', 'MessageController', ['only' => 'store']);
