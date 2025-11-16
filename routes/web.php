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
| Web Routes - ĐÃ ĐƯỢC TỐI ƯU HOÀN TOÀN
|--------------------------------------------------------------------------
*/

// ------------------- TEST MIDDLEWARE -------------------
Route::get('/test-role', fn() => 'Middleware role test OK')
    ->middleware([CheckRole::class . ':admin']);

// === PUBLIC ROUTES ===
Route::get('/', [HomeController::class, 'index'])->name('dashboard');

// === AUTHENTICATED USER ROUTES ===
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

// === ADMIN ROUTES ===
Route::prefix('admin')
    ->middleware(['auth', CheckRole::class . ':admin'])
    ->as('admin.')
    ->group(function () {

        // =========================================================
        // 1. USER MANAGEMENT
        // =========================================================
        // USER MANAGEMENT – ĐÃ FIX 100%
    Route::resource('users', UserController::class);
    
    Route::get('users/deleted', [UserController::class, 'deleted'])
        ->name('users.deleted');
    Route::delete('users/{user}/force-delete', [UserController::class, 'forceDelete'])
        ->name('users.forceDelete');
    Route::post('users/{id}/restore', [UserController::class, 'restore'])
        ->name('users.restore');
    Route::delete('users/{user}/softdelete', [UserController::class, 'softDelete'])
        ->name('users.softdelete');

    

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
                Route::delete('/{club}', 'destroy')->name('destroy');

                Route::get('/{id}/members/filter', 'filterMembers')->name('members.filter');
                Route::get('/members/search', 'searchMembers')->name('members.search');
                Route::post('/search', 'searchJson')->name('search');
                Route::delete('clubs/{club}/members/{member}', [ClubController::class, 'removeMember'])
                    ->name('members.remove');
            });

        Route::get('/club-balance/{clubId}', [FundController::class, 'getClubBalance'])
            ->name('clubs.balance');

        // =========================================================
        // 5. CLUB REQUESTS
        // =========================================================
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
                Route::get('/trash', 'trash')->name('trash');
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::post('/filter', 'filter')->name('filter');
                Route::post('/upload-image', 'uploadImage')->name('uploadImage');
                Route::post('/upload-file', 'uploadFile')->name('uploadFile');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
                Route::patch('/{id}/toggle', 'toggle')->name('toggle');
                Route::put('/{id}/approve', 'approve')->name('approve');
                Route::put('/{id}/reject', 'reject')->name('reject');
                Route::patch('/{id}/restore', 'restore')->name('restore');
                Route::delete('/{id}/force', 'forceDelete')->name('forceDelete');
            });

        // =========================================================
        // 7. DOCUMENT & HISTORY
        // =========================================================
        Route::controller(HistoryController::class)
            ->prefix('history')
            ->as('history.')
            ->group(fn() => Route::get('/', 'index')->name('index'));

        Route::controller(DocumentClubController::class)
            ->prefix('documentclub')
            ->as('documentclub.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/search', 'search')->name('search');
                Route::get('/trash', 'trash')->name('trash');
                Route::post('/{document}/approve', 'approve')->name('approve');
                Route::post('/{document}/reject', 'reject')->name('reject');
                Route::get('/{document}/edit', 'edit')->name('edit');
                Route::put('/{document}', 'update')->name('update');
                Route::delete('/{document}', 'destroy')->name('destroy');
                Route::get('/{document}/download', 'download')->name('download');
                Route::get('/{document}', 'show')->name('show');
                Route::put('/{id}/restore', 'restore')->name('restore');
                Route::delete('/{id}/force', 'forceDelete')->name('forceDelete');
            });

        // =========================================================
        // 8. COMMENT & NOTIFICATION
        // =========================================================
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
            });

        Route::controller(ClubReportController::class)
            ->prefix('clubs/{id}/report')
            ->as('clubs.report.')
            ->group(function () {
                Route::get('/', 'show')->name('show');
                Route::get('/pdf', 'exportPdf')->name('pdf');
            });

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
        Route::post('event_fund_requests/{id}/reject', [EventFundRequestController::class, 'reject'])
            ->name('event_fund_requests.reject');
        Route::post('event_fund_settlements/{id}/approve', [EventFundSettlementController::class, 'approve'])
            ->name('event_fund_settlements.approve');

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
        // 11. TRASH MEDIA
        // =========================================================
        Route::prefix('trash/media')
            ->as('trash.media.')
            ->controller(TrashController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::patch('/{id}/restore', 'restore')->name('restore');
                Route::delete('/{id}/force-delete', 'forceDelete')->name('forceDelete');
            });

        // =========================================================
        // 12. TEST ADMIN
        // =========================================================
        Route::get('/test-role', fn() => 'Bạn có quyền truy cập admin!');
    });

// === AUTH ROUTES ===
require __DIR__ . '/auth.php';