<?php

use App\Http\Controllers\Client\ClubFundController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ClubPostController;
use App\Http\Controllers\Client\ClubRequestController;
use App\Http\Controllers\Client\ClubMemberRequestController;
use App\Http\Controllers\Client\InterviewController;
use App\Http\Controllers\Client\RecruitFormController;
use App\Http\Controllers\Client\ClubMemberController;
use App\Http\Controllers\Client\NotificationController;
use App\Http\Controllers\Client\ClubNotificationController;

// Trang client home — public, user vẫn vào được
Route::name('client.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
   Route::get('/clubs', [HomeController::class, 'showClubs'])->name('clubs.list');
 Route::get('/clubs/{club}', [HomeController::class, 'show'])->name('clubs.show');
});

// Routes dành cho user đăng nhập
Route::middleware(['auth'])->group(function () {
    Route::prefix('club')->name('club.member.')->group(function () {
        Route::get('/{club_id}/view', [ClubMemberController::class, 'view'])->name('view');
    });

    Route::post('/notifications/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});

// Dashboard user bình thường — chỉ client mới vào được
Route::prefix('club-manager')->name('club_manager.')->middleware(['auth', 'club_manager'])->group(function () {

    // Dashboard CLB
    // Route::get('/dashboard/{club_id}', [ClubManagerController::class, 'dashboard'])->name('dashboard');

    // Bài viết CLB
    Route::resource('/{club_id}/posts', ClubPostController::class);

    // Đề xuất sửa thông tin CLB
    Route::prefix('{club_id}/edit-request')->group(function () {
        Route::get('/', [ClubRequestController::class, 'index'])->name('edit_request.index');
        Route::post('/', [ClubRequestController::class, 'store'])->name('edit_request.store');
    });

    Route::prefix('{club_id}/requests')->group(function () {
        Route::get('/', [ClubMemberRequestController::class, 'index'])->name('member_requests.index');
        Route::post('/approve/{member_id}', [ClubMemberRequestController::class, 'approve'])->name('member_requests.approve');
        Route::post('/reject/{request_id}', [ClubMemberRequestController::class, 'reject'])->name('member_requests.reject');
    });

    // Phỏng vấn / điểm danh
    Route::prefix('{club_id}/interviews')->group(function () {
        Route::get('/', [InterviewController::class, 'index'])->name('interviews.index');
        Route::post('/contact', [InterviewController::class, 'contact'])->name('interviews.contact');
        Route::post('/schedule', [InterviewController::class, 'schedule'])->name('interviews.schedule');
        Route::post('/attendance', [InterviewController::class, 'attendance'])->name('interviews.attendance');
    });

    // Form tuyển thành viên
    Route::prefix('{club_id}/recruit')->group(function () {
        Route::get('/form/create', [RecruitFormController::class, 'create'])->name('recruit_form.create');
        Route::post('/form', [RecruitFormController::class, 'store'])->name('recruit_form.store');
        Route::post('/form/question/{question}/toggle', [RecruitFormController::class, 'toggle'])->name('recruit_form.toggle');
        Route::get('/', [RecruitFormController::class, 'index'])->name('recruit.index');
        Route::post('/reject/{request_id}', [RecruitFormController::class, 'reject'])->name('recruit.reject');
        Route::post('/approve/{member_id}', [RecruitFormController::class, 'approve'])->name('recruit.approve');
    });

    Route::prefix('{club_id}/notifications')->name('notifications.')->group(function () {
        Route::get('/create', [ClubNotificationController::class, 'create'])->name('create');
        Route::post('/', [ClubNotificationController::class, 'store'])->name('store');
    });

    // Quỹ CLB
Route::prefix('club/{club_id}/fund')->middleware('auth')->group(function () {
    // Trang tổng quỹ + lịch sử giao dịch
    Route::get('/', [ClubFundController::class, 'index'])->name('fund.index');

    // Tạo giao dịch (thu hoặc chi)
    Route::post('/transactions', [ClubFundController::class, 'storeTransaction'])->name('fund.transactions.store');

    // Duyệt giao dịch
    Route::post('/transactions/{transaction}/approve', [ClubFundController::class, 'approveTransaction'])
        ->name('fund.transactions.approve');

    // Trang cập nhật giao dịch (chỉ cần {transaction}, đã có {club_id} trong prefix)
    Route::get('/transactions/{transaction}/edit', [ClubFundController::class, 'edit'])->name('fund.transactions.edit');
  Route::put('/transactions/{transaction}', [ClubFundController::class, 'update'])
    ->name('fund.transactions.update');

    Route::post('/transactions/{transaction}/complete', [ClubFundController::class, 'completeTransaction'])
        ->name('fund.transactions.complete');

    // Export Excel
    Route::get('/export', [ClubFundController::class, 'export'])->name('fund.export');
});


});
