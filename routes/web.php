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
    TrashController,
    ClubJoinRequestController,
    PlanController,
    DocumentPostController,
    DocumentClubController
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

        // 📰 Post Management
        Route::controller(PostController::class)
            ->prefix('posts')
            ->as('posts.')
            ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::post('/filter', 'filter')->name('filter');
            Route::post('/upload-image', 'uploadImage')->name('uploadImage');
            Route::post('/upload-file', 'uploadFile')->name('uploadFile');
            Route::patch('/{id}/toggle', 'toggle')->name('toggle');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::get('/{id}', 'show')->name('show');
        });

        // 📚 Document Management
        Route::controller(DocumentPostController::class)
            ->prefix('documents')
            ->as('documents.')
            ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/filter', 'filter')->name('filter');
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
                Route::get('/create', 'create')->name('create');         // form thêm
                Route::post('/', 'store')->name('store');
                Route::get('/search', 'search')->name('search'); // realtime search
      // lưu mới
                Route::get('/{document}/edit', 'edit')->name('edit');    // form chỉnh sửa
                Route::put('/{document}', 'update')->name('update');     // cập nhật
                Route::delete('/{document}', 'destroy')->name('destroy'); // xóa mềm
                Route::get('/{document}/download', 'download')->name('download');
                Route::get('/{document}', 'show')->name('show');


                // ✅ Thùng rác và quản lý tài liệu đã xóa mềm
                Route::get('/trash', 'trash')->name('trash'); // danh sách tài liệu đã xóa mềm
                Route::put('/{id}/restore', 'restore')->name('restore'); // khôi phục
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

        // 🏛 Club Management
        Route::controller(ClubController::class)
            ->prefix('clubs')
            ->as('clubs.')
            ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{club}', 'show')->name('show');
            Route::get('/{club}/edit', 'edit')->name('edit');
            Route::put('/{club}', 'update')->name('update');
            Route::delete('/{club}', 'destroy')->name('destroy');
            Route::post('/{club}/approve', 'approve')->name('approve');
            Route::post('/{club}/assign-manager', 'assignManager')->name('assignManager');
            Route::get('/{club}/assign', 'assign')->name('assign');
            Route::post('/{club}/assign', 'assignStore')->name('assign.store');
        });

        // 📝 Club Request Management
        Route::controller(ClubRequestController::class)
            ->prefix('club-requests')
            ->as('club-requests.')
            ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{clubRequest}', 'show')->name('show');
            Route::post('/{clubRequest}/handle', 'handle')->name('handle');
        });

        // 🙋‍♂️ Club Join Request Management
        Route::controller(ClubJoinRequestController::class)
            ->prefix('club-join-requests')
            ->as('club-join-requests.')
            ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{joinRequest}', 'show')->name('show');
            Route::post('/{joinRequest}', 'handle')->name('handle');
        });

        // 🔔 Notification Management
        // 🔔 Notification Management

        Route::controller(NotificationController::class)
            ->prefix('notifications')
            ->as('notifications.')
            ->group(function () {

                // Danh sách, tạo, lưu
                Route::get('/', 'index')->name('index');                   // ✅ danh sách thông báo
                Route::get('/create', 'create')->name('create');           // form tạo thông báo
                Route::post('/', 'store')->name('store');                  // lưu thông báo

                // AJAX fetch dữ liệu liên quan
                Route::get('/fetch-users', 'fetchUsers')->name('fetchUsers');
                Route::get('/fetch-clubs', 'fetchClubs')->name('fetchClubs');
                Route::get('/fetch-club-members', 'fetchClubMembers')->name('fetchClubMembers');
                Route::get('/fetch-events', 'fetchEvents')->name('fetchEvents');
                Route::get('/fetch-event-members', 'fetchEventMembers')->name('fetchEventMembers');

                // Gửi lại thông báo cho từng user theo batch
                Route::post('/resend/{batchId}/{userId}', 'resend')->name('resend');

                // ✅ Xóa thông báo hàng loạt
                Route::post('/bulk-delete', 'bulkDelete')->name('bulkDelete');

            });


        // 📝 Club Request Management

        Route::controller(ClubRequestController::class)
            ->prefix('club-requests')
            ->as('club-requests.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                // Route model binding
                Route::get('/{clubRequest}', 'show')->name('show');
                // PATCH để update status
                Route::patch('/{clubRequest}/update-status', 'updateStatus')->name('updateStatus');
            });



// 🙋‍♂️ Club Join Request Management
Route::controller(ClubJoinRequestController::class)
    ->prefix('club-join-requests')
    ->as('club-join-requests.')
    ->group(function () {
        // Danh sách yêu cầu
        Route::get('/', 'index')->name('index');

        // Xem chi tiết 1 yêu cầu
        Route::get('/{id}', 'show')->name('show');

        // Duyệt yêu cầu
        Route::post('/{id}/approve', 'approve')->name('approve');

        // Từ chối yêu cầu
        Route::post('/{id}/reject', 'reject')->name('reject');

        // Gửi yêu cầu (nếu bạn dùng cho user)
        Route::post('/{club_id}/store', 'store')->name('store');
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




// === 🏛 Club Manager Routes ===
Route::prefix('club-manager')
    ->middleware(['auth', CheckRole::class . ':club_manager'])
    ->as('club-manager.')
    ->group(function () {

        // 💰 Fund Management (Club Manager can only see their club's funds)
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
            Route::get('/api/summary', 'summary')->name('summary');
        });
    });


// === Auth Routes ===
require __DIR__ . '/auth.php';
