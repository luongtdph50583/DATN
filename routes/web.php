<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    ProfileController
};
use App\Http\Controllers\Admin\{
    EventFundRequestController,
    EventFundSettlementController,
    UserController,
    MemberController,
    EventController,
    PostController,
    DocumentController,
    HistoryController,
    CommentController,
    ClubController,
    NotificationController,
    StatisticsController,
    ClubReportController,
    ClubRequestController,
    ClubJoinRequestController,
    PlanController,
    DocumentPostController,
    DocumentClubController,
    ClubLeaveRequestController,
    ClubUpdateLogController,
    ClubRequestUpdateController,
    TrashController,
    ReportController,
    PostUpdateLogController,
    DocumentUpdateLogController
};
use App\Http\Controllers\FundController;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\HomeController as AdminHomeController;
use App\Http\Controllers\Client\HomeController as ClientHomeController;
use App\Http\Controllers\Manager\DocumentController as ManagerDocumentController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;


/*
|--------------------------------------------------------------------------
| Web Routes - ĐÃ ĐƯỢC TỐI ƯU HOÀN TOÀN
|--------------------------------------------------------------------------
*/

// ------------------- TEST MIDDLEWARE -------------------
Route::get('/test-role', fn() => 'Middleware role test OK')
    ->middleware([CheckRole::class . ':admin']);

// === PUBLIC ROUTES ===
Route::get('/', [HomeController::class, 'index'])->name('dashboard');

// === AUTHENTICATED USER ROUTES ===
Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/view', [ProfileController::class, 'show'])->name('show'); // xem profile
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit'); // chỉnh sửa profile
    Route::patch('/edit', [ProfileController::class, 'update'])->name('update');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    Route::get('/avatar', [ProfileController::class, 'editAvatar'])->name('avatar');
Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar.update');

});


Route::get('/', [ClientHomeController::class, 'index'])->name('client.home');
Route::prefix('admin')->middleware([CheckRole::class . ':admin'])->group(function () {
    Route::get('/dashboard', [AdminHomeController::class, 'index'])->name('admin.dashboard');
});
// === ADMIN ROUTES ===
Route::prefix('admin')
    ->middleware(['auth', CheckRole::class . ':admin'])
    ->as('admin.')
    ->group(function () {
        Route::prefix('post-update-logs')
            ->as('post_update_logs.')
            ->group(function () {

                // Danh sách log
                Route::get(
                    '/',
                    [PostUpdateLogController::class, 'index']
                )->name('index');

                // Chi tiết 1 log
                Route::get(
                    '/{id}',
                    [PostUpdateLogController::class, 'show']
                )->name('show');
            });
        Route::prefix('document-update-logs')
            ->as('document_update_logs.')
            ->group(function () {
                // Danh sách log tài liệu
                Route::get('/', [DocumentUpdateLogController::class, 'index'])->name('index');
                // Chi tiết 1 log tài liệu
                Route::get('/{id}', [DocumentUpdateLogController::class, 'show'])->name('show');
                // Route::get('admin/document-update-logs/filter', [DocumentUpdateLogController::class, 'filter'])
                //     ->name('admin.document_update_logs.filter');
            });

        // =========================================================
        // 1. USER MANAGEMENT
        // =========================================================
        // USER MANAGEMENT – ĐÃ FIX 100%
    // Route::resource('users', UserController::class);

    Route::get('users/deleted', [UserController::class, 'deleted'])
        ->name('users.deleted');
    Route::delete('users/{user}/force-delete', [UserController::class, 'forceDelete'])
        ->name('users.forceDelete');
    Route::post('users/{id}/restore', [UserController::class, 'restore'])
        ->name('users.restore');
    Route::delete('users/{user}/softdelete', [UserController::class, 'softDelete'])
        ->name('users.softdelete');

        Route::resource('users', UserController::class);

        // Toggle status
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('users.toggleStatus');

        // =========================================================
        // 2. EVENT MANAGEMENT – ĐÃ HOÀN CHỈNH 100%
        // =========================================================
        // Custom routes PHẢI ĐẶT TRƯỚC resource!!!
        Route::get('events/deleted', [EventController::class, 'deleted'])
            ->name('events.deleted');

        Route::delete('events/{event}/softdelete', [EventController::class, 'softDelete'])
            ->name('events.softdelete');

        Route::post('events/{id}/restore', [EventController::class, 'restore'])->name('events.restore');


        Route::delete('events/{event}/force-delete', [EventController::class, 'forceDelete'])
            ->name('events.forceDelete');

        Route::post('events/{event}/approve', [EventController::class, 'approve'])
            ->name('events.approve');
        Route::post('events/{event}/reject', [EventController::class, 'reject'])
            ->name('events.reject');

        Route::post('events/{event}/pin-top', [EventController::class, 'pinTop'])
            ->name('events.pinTop');

        // AJAX hỗ trợ
        Route::get('events-by-club/{clubId}', [EventController::class, 'getEventsByClub'])
            ->name('events.byClub');
        Route::get('events/get-managers/{clubId}', [EventController::class, 'getManagersByClub'])
            ->name('events.getManagers');
        Route::get('events/club-members/{club}', [EventController::class, 'getClubMembers'])
            ->name('events.club-members');

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/budget/pdf', [ReportController::class, 'budgetPdf'])->name('budget');
            Route::get('/budget/excel', [ReportController::class, 'budgetExcel'])->name('budget.excel');
            Route::get('/events/{event}/attendance-pdf', [ReportController::class, 'attendancePdf'])
                ->name('event.attendance.pdf');
        });
        Route::prefix('admin/events/{event}')->name('admin.events.')->group(function () {
    Route::get('/budget/edit', [EventController::class, 'editBudget'])
        ->name('edit_budget');
    Route::post('/budget/update', [EventController::class, 'updateBudget'])
        ->name('update_budget');
});


        // Resource PHẢI ĐẶT CUỐI CÙNG!!!
        Route::resource('events', EventController::class);

        // =========================================================
        // 3. MEMBER MANAGEMENT
        // =========================================================
        Route::controller(MemberController::class)
            ->prefix('members')
            ->as('members.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');

                // ⚠️ Các route đặc biệt PHẢI đặt trước route có {member}
                Route::get('/trashed', 'trashed')->name('trashed');
                Route::post('/{id}/restore', 'restore')->name('restore');
                Route::delete('/{id}/force-delete', 'forceDelete')->name('forceDelete');

                // Các route có {member} đặt sau cùng
                Route::get('/{member}', 'show')->name('show');
                Route::get('/{member}/edit', 'edit')->name('edit');
                Route::put('/{member}', 'update')->name('update');
                Route::delete('/{member}', 'destroy')->name('destroy');
                Route::post('/{member}/toggle-status', 'toggleStatus')->name('toggleStatus');

                // Export
                Route::get('/export/excel', 'exportExcel')->name('export.excel');
            });


        // =========================================================
        // 4. CLUB MANAGEMENT
        // =========================================================
        // Routes cho CLB




        Route::controller(ClubController::class)
            ->prefix('clubs')
            ->as('clubs.')
            ->group(function () {


                // ♻️ Trang thùng rác (phải để trên /{id})
                Route::get('/trash', 'trash')->name('trash');

                // 🔄 Khôi phục CLB
                Route::patch('/{id}/restore', 'restore')->name('restore');

                // ❌ Xóa vĩnh viễn CLB
                Route::delete('/{id}/force-delete', 'forceDelete')->name('forceDelete');


                // ✅ ⚡ Đặt các route tìm kiếm và filter TRƯỚC route {club}
                Route::get('/members/search', 'searchMembers')->name('members.search');
                Route::post('/search', 'searchJson')->name('search');
                Route::get('/{club}/members/filter', 'filterMembers')->name('members.filter');

                // ✅ CRUD chính
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');

                // ⚠️ Các route có {club} để SAU CÙNG
                Route::get('/{club}', 'show')->name('show');
                Route::get('/{club}/edit', 'edit')->name('edit');
                Route::put('/{club}', 'update')->name('update');
                Route::delete('/{club}', 'destroy')->name('destroy');


                // Xóa thành viên
                Route::delete('/{club}/members/{member}', 'removeMember')->name('members.remove');
            });




        Route::get('/club-balance/{clubId}', [FundController::class, 'getClubBalance'])
            ->name('clubs.balance');

        // =========================================================
        // 5. CLUB REQUESTS
        // =========================================================
        // Routes cho yêu cầu thành lập CLB

        Route::controller(ClubRequestController::class)
            ->prefix('club-requests')
            ->as('club_requests.')
            ->group(function () {
                Route::get('/filter', 'filterRequests')->name('filter');
                Route::get('/', 'indexRequests')->name('index');
                Route::get('/{id}', 'showRequest')->name('show');
                Route::get('/{id}/show2', 'show2')->name('show2');
                Route::post('/{id}/handle', 'handleRequest')->name('handle');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });
        Route::controller(ClubRequestUpdateController::class)
            ->prefix('club-requests-update')
            ->as('club_requests_update.')
            ->group(function () {
                Route::get('/filter', 'filterRequests')->name('filter');
                Route::get('/', 'indexRequests')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'showRequest')->name('show');
                Route::get('/{id}/show2', 'show2')->name('show2');
                Route::post('/{id}/handle', 'handleUpdateRequest')->name('handleUpdateRequest');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

        Route::controller(ClubJoinRequestController::class)
            ->prefix('club-join-requests')
            ->as('club_join_requests.')
            ->group(function () {
                Route::get('/filter', 'filter')->name('filter');
                Route::get('/', 'index')->name('index');
                Route::get('/{id}', 'showRequest')->name('show');
                Route::get('/{id}/full', 'show2')->name('show2');

                // ✅ Xử lý từng trạng thái trong quy trình duyệt
                Route::post('/{id}/handle', 'handle')->name('handle');

                // ✅ Xóa yêu cầu
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

        Route::controller(ClubLeaveRequestController::class)
            ->prefix('club-leave-requests')
            ->as('club_leave_requests.')
            ->group(function () {
                Route::get('/filter', 'filter')->name('filter');
                Route::get('/', 'index')->name('index');
                Route::get('/{id}/full', 'show2')->name('show2');
                Route::get('/{id}', 'showRequest')->name('show');
                Route::post('/{id}/handle', 'handleRequest')->name('handle');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

        // =========================================================
        // 6. POST MANAGEMENT
        // =========================================================
        Route::controller(PostController::class)
            ->prefix('posts')
            ->as('posts.')
            ->group(function () {
            Route::get('/trash', 'trash')->name('trash');                 // Danh sách bài viết đã xóa
            Route::get('/', 'index')->name('index');                      // Danh sách bài viết
            Route::get('/create', 'create')->name('create');              // Form tạo bài viết
            Route::post('/', 'store')->name('store');                     // Lưu bài viết mới
            Route::post('/filter', 'filter')->name('filter');             // Lọc bài viết
            Route::post('/upload-image', 'uploadImage')->name('uploadImage'); // Upload ảnh từ editor
            Route::post('/upload-file', 'uploadFile')->name('uploadFile');    // Upload file từ editor
    
            Route::get('/{id}', 'show')->name('show');                    // Xem chi tiết
            Route::get('/{id}/trash', 'showTrash')->name('showTrash');    // Xem chi tiết bài viết đã xóa
            Route::get('/{id}/edit', 'edit')->name('edit');               // Form sửa
            Route::put('/{id}', 'update')->name('update');                // Cập nhật bài viết
            Route::delete('/{id}', 'destroy')->name('destroy');           // Xóa mềm bài viết
            Route::patch('/{id}/toggle', 'toggle')->name('toggle');       // Ẩn/hiện bài viết
            Route::put('/{id}/approve', 'approve')->name('approve');      // Duyệt bài viết
            Route::put('/{id}/reject', 'reject')->name('reject');         // Từ chối bài viết
            Route::patch('/{id}/restore', 'restore')->name('restore');    // Khôi phục bài viết
            Route::delete('/{id}/force', 'forceDelete')->name('forceDelete'); // Xóa vĩnh viễn
        });


        // =========================================================
        // 7. DOCUMENT & HISTORY
        // =========================================================
        Route::controller(HistoryController::class)
            ->prefix('history')
            ->as('history.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
            });



        Route::controller(DocumentClubController::class)
            ->prefix('documentclub')
            ->as('documentclub.')
            ->group(function () {

                Route::get('/', 'index')->name('index');                 // danh sách
                Route::get('/create', 'create')->name('create');         // form thêm mới
                Route::post('/', 'store')->name('store');                // lưu mới
                Route::get('/search', 'search')->name('search');         // realtime search
                Route::get('/trash', 'trash')->name('trash');            // thùng rác


                Route::post('/{document}/approve', 'approve')->name('approve');   // duyệt
                Route::post('/{document}/reject', 'reject')->name('reject');      // từ chối

                Route::get('/{document}/edit', 'edit')->name('edit');             // form chỉnh sửa
                Route::put('/{document}', 'update')->name('update');              // cập nhật
                Route::delete('/{document}', 'destroy')->name('destroy');         // xóa mềm
                Route::get('/{document}/download', 'download')->name('download'); // tải xuống
                Route::get('/{document}', 'show')->name('show');                  // xem chi tiết

                Route::put('/{id}/restore', 'restore')->name('restore');          // khôi phục
                Route::delete('/{id}/force', 'forceDelete')->name('forceDelete');
                Route::get('/trash/{id}', 'showTrash')->name('showTrash'); // xem chi tiết tài liệu đã xoá // xóa vĩnh viễn
            });

        // =========================================================
        // 8. COMMENT & NOTIFICATION
        // =========================================================
        Route::controller(CommentController::class)
            ->prefix('comments')
            ->as('comments.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/trashed', 'trashed')->name('trashed'); // danh sách đã xóa
                Route::get('/{comment}', 'show')->name('show');
                Route::delete('/{comment}', 'destroy')->name('destroy'); // xóa mềm
                Route::post('/{comment}/toggle-status', 'toggleStatus')->name('toggleStatus');
                Route::post('/{comment}/restore', 'restore')->name('restore'); // khôi phục
                Route::delete('/{comment}/force-delete', 'forceDelete')->name('forceDelete'); // xóa vĩnh viễn
            });


        Route::controller(NotificationController::class)
            ->prefix('notifications')
            ->as('notifications.')
            ->group(function () {

                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');

                Route::get('/fetch-users', 'fetchUsers')->name('fetchUsers');
                Route::get('/fetch-clubs', 'fetchClubs')->name('fetchClubs');
                Route::get('/fetch-club-members', 'fetchClubMembers')->name('fetchClubMembers');
                Route::get('/fetch-events', 'fetchEvents')->name('fetchEvents');
                Route::get('/fetch-event-members', 'fetchEventMembers')->name('fetchEventMembers');
                Route::post('/resend/{batchId}/{userId}', 'resend')->name('resend');
                Route::post('/bulk-delete', 'bulkDelete')->name('bulkDelete');

                // ✅ THÊM MỚI
                Route::post('/mark-read/{id}', 'markRead')->name('markRead');       // đánh dấu đã đọc
                Route::delete('/{id}', 'delete')->name('delete');                   // xoá 1 thông báo
                Route::get('/go/{id}', 'go')->name('go');                            // xóa thông báo
            });


        // =========================================================
        // 9. STATISTICS & REPORTS
        // =========================================================
        Route::controller(StatisticsController::class)
            ->prefix('stats')
            ->as('stats.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/events', 'events')->name('events');
                Route::get('/clubs', 'clubs')->name('clubs');
                Route::get('/members', 'members')->name('members');
                Route::get('/accounts', 'accounts')->name('accounts');
                Route::get('/posts', 'posts')->name('posts');
                Route::get('/funds', 'fundRequests')->name('funds');
            });

        Route::controller(ClubReportController::class)
            ->prefix('clubs/{id}/report')
            ->as('clubs.report.')
            ->group(function () {
                Route::get('/', 'show')->name('show');
                Route::get('/pdf', 'exportPdf')->name('pdf');
            });
        Route::get('/stats/events/pdf', [StatisticsController::class, 'exportPdf'])->name('stats.events.pdf');

        Route::get('/stats/clubs/pdf', [StatisticsController::class, 'clubsPdf'])
            ->name('stats.clubs.pdf');

        Route::get('/stats/accounts/pdf', [StatisticsController::class, 'accountsPdf'])
            ->name('stats.accounts.pdf');
        Route::get('/stats/funds/pdf', [StatisticsController::class, 'fundsPdf'])
            ->name('stats.funds.pdf');


        Route::controller(StatisticsController::class)
            ->prefix('statistics-and-reports')
            ->as('statistics-and-reports.')
            ->group(function () {
                Route::get('/statistics', 'index')->name('statistics');
                Route::get('/reports', 'reports')->name('reports');
            });

        // =========================================================
        // 10. FUND & PLAN
        // =========================================================
        Route::controller(FundController::class)
            ->prefix('funds')
            ->as('funds.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{fund}', 'show')->name('show');
                Route::get('/{fund}/edit', 'edit')->name('edit');
                Route::put('/{fund}', 'update')->name('update');
                Route::delete('/{fund}', 'destroy')->name('destroy');
                Route::post('/{fund}/approve', 'approve')->name('approve');
                Route::post('/{fund}/reject', 'reject')->name('reject');
                Route::get('/api/summary', 'summary')->name('summary');
            });

        Route::resource('event_fund_requests', EventFundRequestController::class);
        Route::resource('event_fund_settlements', EventFundSettlementController::class);

        Route::get('event_fund_requests/{id}/approve', [EventFundRequestController::class, 'approveForm'])
            ->name('event_fund_requests.approveForm');
        Route::post('event_fund_requests/{id}/approve', [EventFundRequestController::class, 'approve'])
            ->name('event_fund_requests.approve');
        Route::post('event_fund_settlements/{id}/approve', [EventFundSettlementController::class, 'approve'])
            ->name('event_fund_settlements.approve');

        Route::get('event_fund_requests/{id}/reject', [EventFundRequestController::class, 'showRejectForm'])
            ->name('event_fund_requests.reject');

        // Xử lý POST từ chối
        Route::post('event_fund_requests/{id}/reject', [EventFundRequestController::class, 'reject'])
            ->name('event_fund_requests.reject.submit');
        Route::get(
            '/event-fund-requests/{id}/disbursing',
            [EventFundRequestController::class, 'disbursing']
        )
            ->name('event_fund_requests.disbursing');

        Route::post(
            '/event-fund-requests/{id}/update-disbursement',
            [EventFundRequestController::class, 'updateDisbursement']
        )
            ->name('event_fund_requests.updateDisbursement');

Route::post('/event-fund-requests/{id}/complete-disbursement',
    [EventFundRequestController::class, 'completeDisbursement'])
    ->name('event_fund_requests.completeDisbursement');



        Route::controller(PlanController::class)
            ->prefix('plans')
            ->as('plans.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{plan}', 'show')->name('show');
                Route::get('/{plan}/edit', 'edit')->name('edit');
                Route::put('/{plan}', 'update')->name('update');
                Route::delete('/{plan}', 'destroy')->name('destroy');
                Route::post('/{plan}/approve', 'approve')->name('approve');
                Route::post('/{plan}/reject', 'reject')->name('reject');
            });

        // =========================================================
        // 11. CLUB UPDATE LOGS
        // =========================================================
        Route::controller(ClubUpdateLogController::class)
            ->prefix('club-update-logs')
            ->as('club_update_logs.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{log}', 'show')->name('show');
            });

        // =========================================================
        // 12. TRASH MEDIA
        // =========================================================
        // Route::prefix('trash/media')
        //     ->as('trash.media.')
        //     ->controller(TrashController::class)
        //     ->group(function () {
        //     Route::get('/', 'index')->name('index');
        //     Route::patch('/{id}/restore', 'restore')->name('restore');
        //     Route::delete('/{id}/force-delete', 'forceDelete')->name('forceDelete');
        // });

        // =========================================================
        // 13. TEST ADMIN
        // =========================================================
        Route::get('/test-role', fn() => 'Bạn có quyền truy cập admin!');
    });
    Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->as('admin.')
    ->group(function () {

    Route::resource('club_requests', ClubRequestController::class);
    // → Tạo tự động: index, create, store, show, edit, update, destroy
    });


    // ═══════════════════════════════════════════════════════════════════
// CLUB MANAGER ROUTES – QUẢN LÝ SỰ KIỆN CLB (CHỦ NHIỆM CLB)
// ═══════════════════════════════════════════════════════════════════
Route::prefix('club-manager')
    ->middleware(['auth', 'role:club_manager'])
    ->as('club.')
    ->group(function () {

    // Trang chủ quản lý sự kiện
    Route::get('/events', [App\Http\Controllers\ClubManager\EventController::class, 'index'])
        ->name('events.index');

    Route::get('/events/create', [App\Http\Controllers\ClubManager\EventController::class, 'create'])
        ->name('events.create');

    Route::post('/events', [App\Http\Controllers\ClubManager\EventController::class, 'store'])
        ->name('events.store');

    Route::get('/events/{id}/registrations', [App\Http\Controllers\ClubManager\EventController::class, 'registrations'])
        ->name('events.registrations');

    Route::post('/registrations/{id}/approve', [App\Http\Controllers\ClubManager\EventController::class, 'approveRegistration'])
        ->name('registrations.approve');

    Route::post('/registrations/{id}/reject', [App\Http\Controllers\ClubManager\EventController::class, 'rejectRegistration'])
        ->name('registrations.reject');

    Route::get('/events/{id}/attendance', [App\Http\Controllers\ClubManager\EventController::class, 'attendance'])
        ->name('events.attendance');

    Route::get('/events/{id}/checkin', [App\Http\Controllers\ClubManager\EventController::class, 'checkin'])
        ->name('events.checkin');
});
// === AUTH ROUTES ===
require __DIR__ . '/auth.php';
// Route::prefix('manager/document')
    // ->name('manager.document.')
    // ->middleware(['auth', 'role:club_manager'])
    // ->group(function () {
    //     Route::get('/', [ManagerDocumentController::class, 'index'])->name('index');
    //     Route::get('/create', [ManagerDocumentController::class, 'create'])->name('create');
    //     Route::post('/store', [ManagerDocumentController::class, 'store'])->name('store');
    //     Route::get('/{id}/edit', [ManagerDocumentController::class, 'edit'])->name('edit');
    //     Route::put('/{id}', [ManagerDocumentController::class, 'update'])->name('update');
    //     Route::delete('/{id}', [ManagerDocumentController::class, 'destroy'])->name('destroy');
    //     Route::get('/trash', [ManagerDocumentController::class, 'trash'])->name('trash');
    //     Route::patch('/{id}/restore', [ManagerDocumentController::class, 'restore'])->name('restore');
    //     Route::get('/{id}/download', [ManagerDocumentController::class, 'download'])->name('download');
    //     Route::get('/{id}', [ManagerDocumentController::class, 'show'])->name('show');
    //     Route::get('/search/ajax', [ManagerDocumentController::class, 'search'])->name('search.ajax');
    // });

