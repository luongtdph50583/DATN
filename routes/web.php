<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\MemberController;

use App\Http\Controllers\Admin\ClubController;

use Illuminate\Support\Facades\Auth;
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
    //clb
    Route::prefix('clubs')->group(function () {
        Route::get('/', [ClubController::class, 'index'])->name('admin.clubs.index');
        Route::get('/create', [ClubController::class, 'create'])->name('admin.clubs.create');
        Route::post('/', [ClubController::class, 'store'])->name('admin.clubs.store');
        Route::put('/{club}', [ClubController::class, 'update'])->name('admin.clubs.update');
        Route::delete('/{club}', [ClubController::class, 'destroy'])->name('admin.clubs.destroy');
        Route::post('/{club}/assign-manager', [ClubController::class, 'assignManager'])->name('admin.clubs.assignManager');
    });

    Route::prefix('club-requests')->group(function () {
        Route::get('/', [ClubController::class, 'showRequests'])->name('admin.club-requests.index');
        Route::post('/{clubRequest}', [ClubController::class, 'handleRequest'])->name('admin.club-requests.handle');
    });

    Route::prefix('club-join-requests')->group(function () {
        Route::get('/', [ClubController::class, 'showJoinRequests'])->name('admin.club-join-requests.index');
        Route::post('/{clubJoinRequest}', [ClubController::class, 'handleJoinRequest'])->name('admin.club-join-requests.handle');
    });
});

// Nhóm route cho người dùng đã đăng nhập (không phải admin)
Route::middleware('auth')->group(function () {
    // Quản lý hồ sơ cá nhân
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
//CLB

// Include các route authentication
require __DIR__ . '/auth.php';