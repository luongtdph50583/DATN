<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ClubFundController;
use App\Http\Controllers\Client\ClubController;

// Prefix tên route client
Route::name('client.')->group(function () {

    // Trang chủ client
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // -------------------------------
    // Quản lý quỹ CLB (chỉ treasurer)
    // -------------------------------
    Route::middleware(['auth'])->group(function () {
        Route::get('/clubs/{club}/fund', [ClubFundController::class, 'show'])
            ->name('clubs.fund.show');
    });
      Route::get('/my-clubs', [ClubController::class, 'myClubs'])->name('clubs.my');

});
