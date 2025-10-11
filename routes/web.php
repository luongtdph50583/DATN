<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\MemberController;
use Illuminate\Support\Facades\Route;

// Route cho trang dashboard (không yêu cầu admin)
Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

// Nhóm route dành cho admin với tiền tố /admin và middleware auth
Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Quản lý người dùng
    Route::resource('users', UserController::class)->names('admin.users');
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggleStatus');

    // Quản lý sự kiện
    Route::resource('events', EventController::class)->names('admin.events');

    // Quản lý thành viên
    Route::get('/members', [MemberController::class, 'index'])->name('admin.members.index');
});

// Nhóm route cho người dùng đã đăng nhập (không phải admin)
Route::middleware('auth')->group(function () {
    // Quản lý hồ sơ cá nhân
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Include các route authentication
require __DIR__ . '/auth.php';