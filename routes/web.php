<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HomeController;

// Trang chủ/Dashboard
  Route::get('/', [HomeController::class, 'index'])->name('home');

  // Route cho quản lý người dùng
  Route::prefix('admin')->middleware(['auth'])->group(function () {
      Route::resource('users', UserController::class)->names('admin.users');
  });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
