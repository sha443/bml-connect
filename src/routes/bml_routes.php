<?php

Route::get('/payment', '\SHA443\BMLConnect\Http\Controllers\BMLConnectController@createTransaction')->name('test');
Route::get('/response', '\SHA443\BMLConnect\Http\Controllers\BMLConnectController@handleResponse')->name('handleBMLResponse');
