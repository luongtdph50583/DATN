<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\HomeController;

Route::name('client.')->group(function () {

    // Route giả cho trang chủ
    Route::get('/', [HomeController::class, 'index'])->name('home');

});
