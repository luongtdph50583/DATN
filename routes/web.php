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
    ClubLeaveRequestController
};
use App\Http\Controllers\FundController;
use App\Http\Middleware\CheckRole;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// -------------------
// Route test middleware role
// -------------------
Route::get('/test-role', function () {
    return 'Middleware role test OK';
})->middleware([CheckRole::class . ':admin']);

// === 🏠 Public Routes ===
Route::get('/', [HomeController::class, 'index'])->name('dashboard');

// === 👤 Authenticated User Routes ===
Route::middleware(['auth'])->group(function () {
    Route::controller(ProfileController::class)
        ->prefix('profile')
        ->name('profile.')
        ->group(function () {
            Route::get('/', 'edit')->name('edit');
            Route::patch('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
        });
});

// === 🔐 Admin Routes ===
Route::prefix('admin')
    ->middleware(['auth', CheckRole::class . ':admin'])
    ->as('admin.')
    ->group(function () {

        // 👥 User Management
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');

        // 📅 Event Management
        Route::resource('events', EventController::class);
        Route::post('events/{event}/approve', [EventController::class, 'approve'])->name('events.approve');
        Route::post('events/{event}/reject', [EventController::class, 'reject'])->name('events.reject');
        Route::get('events-by-club/{clubId}', [EventController::class, 'getEventsByClub'])
            ->name('events.byClub');
        Route::get('/events/get-managers/{clubId}', [EventController::class, 'getManagersByClub'])->name('events.getManagers');


        Route::get('/club-balance/{clubId}', [FundController::class, 'getClubBalance'])
            ->name('clubs.balance');
        // 👨‍👩‍👧‍👦 Member Management
        Route::controller(MemberController::class)
            ->prefix('members')
            ->as('members.')
            ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{member}', 'show')->name('show');
            Route::get('/{member}/edit', 'edit')->name('edit');
            Route::put('/{member}', 'update')->name('update');
            Route::delete('/{member}', 'destroy')->name('destroy');
            Route::post('/{member}/toggle-status', 'toggleStatus')->name('toggleStatus');
            Route::get('/export/excel', 'exportExcel')->name('export.excel');
        });


        // Routes cho CLB
    
        Route::controller(ClubController::class)
            ->prefix('clubs')
            ->as('clubs.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{club}', 'update')->name('update');

                // ⚙️ AJAX: lọc thành viên trong CLB cụ thể
                Route::get('/{id}/members/filter', 'filterMembers')->name('members.filter');

                // ✅ AJAX: lọc tất cả thành viên hệ thống
                Route::get('/members/search', 'searchMembers')->name('members.search');

                // 🔍 AJAX: tìm kiếm câu lạc bộ real-time
                Route::post('/search', 'searchJson')->name('search');

                // 🗑️ Xóa CLB
                Route::delete('/{club}', 'destroy')->name('destroy');
            });

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

        Route::controller(ClubJoinRequestController::class)
            ->prefix('club-join-requests')
            ->as('club_join_requests.')
            ->group(function () {
                Route::get('/filter', 'filter')->name('filter');
                Route::get('/', 'index')->name('index');
                Route::get('/{id}', 'showRequest')->name('show');
                Route::get('/{id}/full', 'show2')->name('show2');
                Route::post('/{id}/handle', 'handleRequest')->name('handle');
                Route::delete('/{id}', 'destroy')->name('destroy'); // ✅ Xóa
        
            });
        Route::controller(ClubLeaveRequestController::class)
            ->prefix('club-leave-requests')
            ->as('club_leave_requests.')
            ->group(function () {
                Route::get('/filter', 'filter')->name('filter'); // AJAX filter
                Route::get('/', 'index')->name('index');         // Danh sách
                Route::get('/{id}/full', 'show2')->name('show2'); // Trang chi tiết riêng (đặt trước {id})
                Route::get('/{id}', 'showRequest')->name('show'); // Chi tiết offcanvas
                Route::post('/{id}/handle', 'handleRequest')->name('handle'); // Duyệt / từ chối
                Route::delete('/{id}', 'destroy')->name('destroy'); // Xóa
            });
        // 📰 Post Management
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
            Route::get('/{id}/edit', 'edit')->name('edit');               // Form sửa
            Route::put('/{id}', 'update')->name('update');                // Cập nhật bài viết
            Route::delete('/{id}', 'destroy')->name('destroy');           // Xóa mềm bài viết
            Route::patch('/{id}/toggle', 'toggle')->name('toggle');       // Ẩn/hiện bài viết
            Route::put('/{id}/approve', 'approve')->name('approve');      // Duyệt bài viết
            Route::put('/{id}/reject', 'reject')->name('reject');         // Từ chối bài viết
            Route::patch('/{id}/restore', 'restore')->name('restore');    // Khôi phục bài viết
            Route::delete('/{id}/force', 'forceDelete')->name('forceDelete'); // Xóa vĩnh viễn
        });

        // 🕓 History Management
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
                Route::delete('/{id}/force', 'forceDelete')->name('forceDelete'); // xóa vĩnh viễn
            });





        // 💬 Comment Management
        Route::controller(CommentController::class)
            ->prefix('comments')
            ->as('comments.')
            ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{comment}', 'show')->name('show');
            Route::delete('/{comment}', 'destroy')->name('destroy');
            Route::post('/{comment}/toggle-status', 'toggleStatus')->name('toggleStatus');
        });
    
        Route::controller(NotificationController::class)
            ->prefix('notifications')
            ->as('notifications.')
            ->group(function () {
                Route::get('/', 'index')->name('index');                   // ✅ danh sách thông báo
                Route::get('/create', 'create')->name('create');           // form tạo thông báo
                Route::post('/', 'store')->name('store');                  // lưu thông báo
    
                Route::get('/fetch-users', 'fetchUsers')->name('fetchUsers');
                Route::get('/fetch-clubs', 'fetchClubs')->name('fetchClubs');
                Route::get('/fetch-club-members', 'fetchClubMembers')->name('fetchClubMembers');
                Route::get('/fetch-events', 'fetchEvents')->name('fetchEvents');
                Route::get('/fetch-event-members', 'fetchEventMembers')->name('fetchEventMembers');
                Route::post('/resend/{batchId}/{userId}', 'resend')->name('resend');
                Route::post('/bulk-delete', 'bulkDelete')->name('bulkDelete');

            });

        // 📊 Statistics Management
        Route::controller(StatisticsController::class)
            ->prefix('stats')
            ->as('stats.')
            ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/events', 'events')->name('events');
            Route::get('/clubs', 'clubs')->name('clubs');
            Route::get('/members', 'members')->name('members');
        });

        // 📋 Club Report Management
        Route::controller(ClubReportController::class)
            ->prefix('clubs/{id}/report')
            ->as('clubs.report.')
            ->group(function () {
            Route::get('/', 'show')->name('show');
            Route::get('/pdf', 'exportPdf')->name('pdf');
        });

        // 📈 Statistics and Reports Management
        Route::controller(StatisticsController::class)
            ->prefix('statistics-and-reports')
            ->as('statistics-and-reports.')
            ->group(function () {
            Route::get('/statistics', 'index')->name('statistics');
            Route::get('/reports', 'reports')->name('reports');
        });

        // 💰 Fund Management
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

 Route::get('event_fund_requests/{id}/approve', [EventFundRequestController::class, 'approveForm'])->name('event_fund_requests.approveForm');
    Route::post('event_fund_requests/{id}/approve', [EventFundRequestController::class, 'approve'])->name('event_fund_requests.approve');
Route::post('event_fund_requests/{id}/reject', [App\Http\Controllers\Admin\EventFundRequestController::class, 'reject'])
    ->name('event_fund_requests.reject');
    Route::post('event_fund_settlements/{id}/approve', [EventFundSettlementController::class, 'approve'])
    ->name('event_fund_settlements.approve');
        // 🗑️ Trash Management
        Route::prefix('trash/media')
            ->as('trash.media.')
            ->controller(TrashController::class)
            ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::patch('/{id}/restore', 'restore')->name('restore');
            Route::delete('/{id}/force-delete', 'forceDelete')->name('forceDelete');
        });

        // Route test admin
        Route::get('/test-role', function () {
            return 'Bạn có quyền truy cập admin!';
        });

        // 📋 Club Plan Management
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
    });


// === Auth Routes ===
require __DIR__ . '/auth.php';
