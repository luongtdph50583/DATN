<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ClubFundController;
use App\Http\Controllers\Client\ClubPostController;
use App\Http\Controllers\Client\JoinClubController;
use App\Http\Controllers\Client\InterviewController;
use App\Http\Controllers\Client\ClubMemberController;
use App\Http\Controllers\Client\ClubRequestController;
use App\Http\Controllers\Client\RecruitFormController;
use App\Http\Controllers\Client\ClubDocumentController;
use App\Http\Controllers\Client\NotificationController;
use App\Http\Controllers\Client\ClubNotificationController;
use App\Http\Controllers\Client\ClubMemberRequestController;
use App\Http\Controllers\Client\DocumentUpdateLogController;
use App\Http\Controllers\Client\ClubFormationRequestController;

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
        Route::get('/{club_id}/join', [ClubMemberController::class, 'showJoinForm'])->name('join');
        Route::post('/{club_id}/join', [ClubMemberController::class, 'submitJoinRequest'])->name('join.submit');
    });

    Route::post('/notifications/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

     Route::get('/formation-request/create', [ClubFormationRequestController::class, 'create'])
        ->name('formation_request.create');

    // Xử lý gửi yêu cầu
    Route::post('/formation-request/store', [ClubFormationRequestController::class, 'store'])
        ->name('formation_request.store');

 Route::get('/formation-request', [ClubFormationRequestController::class, 'index'])->name('formation-request.index');
    Route::get('/formation-request/{request}', [ClubFormationRequestController::class, 'show'])->name('formation-request.show');
    // Đăng ký tham gia CLB
 Route::get('clubs/{club}/join', [JoinClubController::class, 'showForm'])->name('clubs.join.form');
Route::post('clubs/{club}/join', [JoinClubController::class, 'submitForm'])->name('clubs.join.submit');

});

// Dashboard user bình thường — chỉ client mới vào được
Route::prefix('club-manager')->name('club_manager.')->middleware(['auth', 'club_manager'])->group(function () {

    Route::prefix('/{club_id}/documents')->middleware('auth')->name('club.documents.')->group(function () {
        Route::get('/', [ClubDocumentController::class, 'index'])->name('index');
        Route::get('/create', [ClubDocumentController::class, 'create'])->name('create');
        Route::get('/{id}', [ClubDocumentController::class, 'view'])->name('view');
        Route::get('/{id}/edit', [ClubDocumentController::class, 'edit'])->name('edit');
        Route::post('/store', [ClubDocumentController::class, 'store'])->name('store');
        Route::put('/{id}', [ClubDocumentController::class, 'update'])->name('update');
        Route::delete('/{id}', [ClubDocumentController::class, 'destroy'])->name('destroy');
    });

    // ✅ Thêm route cho logs
  


    // Dashboard CLB
    // Route::get('/dashboard/{club_id}', [ClubManagerController::class, 'dashboard'])->name('dashboard');

    // Bài viết CLB
    Route::resource('/{club_id}/posts', ClubPostController::class);
    Route::post('/{club_id}/posts/upload-image', [ClubPostController::class, 'uploadImage'])->name('posts.upload_image');
    Route::post('/{club_id}/posts/upload-file', [ClubPostController::class, 'uploadFile'])->name('posts.upload_file');

    // Đề xuất sửa thông tin CLB
    Route::prefix('{club_id}/edit-request')->group(function () {
        Route::get('/', [ClubRequestController::class, 'index'])->name('edit_request.index');
        Route::post('/', [ClubRequestController::class, 'store'])->name('edit_request.store');
    });

    Route::prefix('{club_id}/requests')->group(function () {
        Route::get('/', [ClubMemberRequestController::class, 'index'])->name('member_requests.index');
        Route::get('/{request_id}', [ClubMemberRequestController::class, 'show'])->name('member_requests.show');
        Route::post('/approve/{request_id}', [ClubMemberRequestController::class, 'approve'])->name('member_requests.approve');
        Route::post('/reject/{request_id}', [ClubMemberRequestController::class, 'reject'])->name('member_requests.reject');
        Route::post('/handle/{request_id}', [ClubMemberRequestController::class, 'handle'])->name('member_requests.handle');
        Route::post('/batch-schedule', [ClubMemberRequestController::class, 'batchSchedule'])->name('member_requests.batch_schedule');
        Route::post('/batch-complete', [ClubMemberRequestController::class, 'batchComplete'])->name('member_requests.batch_complete');
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
        // Quản lý forms
        Route::get('/forms', [RecruitFormController::class, 'listForms'])->name('recruit_forms.list');
        Route::get('/forms/create', [RecruitFormController::class, 'createForm'])->name('recruit_forms.create');
        Route::post('/forms', [RecruitFormController::class, 'storeForm'])->name('recruit_forms.store');
        Route::delete('/forms/{form_id}', [RecruitFormController::class, 'destroyForm'])->name('recruit_forms.destroy');
        Route::post('/forms/{form_id}/set-default', [RecruitFormController::class, 'setDefault'])->name('recruit_forms.set_default');
        
        // Quản lý câu hỏi trong form
        Route::get('/form/{form_id?}', [RecruitFormController::class, 'create'])->name('recruit_form.create');
        Route::post('/form', [RecruitFormController::class, 'store'])->name('recruit_form.store');
        Route::post('/form/question/{question}/toggle', [RecruitFormController::class, 'toggle'])->name('recruit_form.toggle');
        
        // Yêu cầu tham gia
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
    // Document routes for client
 



});
