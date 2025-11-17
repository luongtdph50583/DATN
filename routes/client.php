<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\HomeController;

// Trang client home — public, user vẫn vào được
Route::name('client.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
});

// Dashboard user bình thường — chỉ client mới vào được



// Trang client home — public, user vẫn vào được
Route::name('client.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
});

Route::prefix('club-manager')->name('club_manager.')->middleware(['auth', 'club.manager'])->group(function() {

    // Dashboard CLB
    // Route::get('/dashboard/{club_id}', [ClubManagerController::class, 'dashboard'])->name('dashboard');

    // // Bài viết CLB
    // Route::resource('/{club_id}/posts', ClubPostController::class);

    // Đề xuất sửa thông tin CLB
    // Route::prefix('{club_id}/edit-request')->group(function() {
    //     Route::get('/', [ClubRequestController::class, 'index'])->name('edit_request.index');
    //     Route::post('/', [ClubRequestController::class, 'store'])->name('edit_request.store');
    // });

    // Duyệt yêu cầu thành viên
    // Route::prefix('{club_id}/requests')->group(function() {
    //     Route::get('/', [ClubMemberRequestController::class, 'index'])->name('member_requests.index');
    //     Route::post('/approve/{member_id}', [ClubMemberRequestController::class, 'approve'])->name('member_requests.approve');
    // });

    // Phỏng vấn / điểm danh
    // Route::prefix('{club_id}/interviews')->group(function() {
    //     Route::get('/', [InterviewController::class, 'index'])->name('interviews.index');
    //     Route::post('/schedule', [InterviewController::class, 'schedule'])->name('interviews.schedule');
    //     Route::post('/attendance', [InterviewController::class, 'attendance'])->name('interviews.attendance');
    // });

    // Form tuyển thành viên
    // Route::prefix('{club_id}/recruit')->group(function() {
    //     Route::get('/form/create', [RecruitFormController::class, 'create'])->name('recruit_form.create');
    //     Route::post('/form', [RecruitFormController::class, 'store'])->name('recruit_form.store');
    //     Route::get('/', [RecruitFormController::class, 'index'])->name('recruit.index');
    //     Route::post('/approve/{member_id}', [RecruitFormController::class, 'approve'])->name('recruit.approve');
    // });
});


