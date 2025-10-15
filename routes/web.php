<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    ProfileController
};
use App\Http\Controllers\Admin\{
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
    ClubReportController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Nơi định nghĩa tất cả route của ứng dụng
|--------------------------------------------------------------------------
*/

// === 🏠 Public Routes ===
Route::get('/', [HomeController::class, 'index'])->name('dashboard');


// === 👤 Authenticated User Routes ===
Route::middleware(['auth'])->group(function () {
    // Profile Management
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });
});

// === 🔐 Admin Routes ===
Route::prefix('admin')->middleware(['auth', 'role:admin'])->as('admin.')->group(function () {
    // 👥 User Management
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');

    // 📅 Event Management
    Route::resource('events', EventController::class);
    Route::post('events/{event}/approve', [EventController::class, 'approve'])->name('events.approve');
    Route::post('events/{event}/reject', [EventController::class, 'reject'])->name('events.reject');

    // 👨‍👩‍👧‍👦 Member Management
    Route::controller(MemberController::class)->prefix('members')->as('members.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/{member}/toggle-status', 'toggleStatus')->name('toggleStatus');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{member}', 'show')->name('show');
        Route::get('/{member}/edit', 'edit')->name('edit');
        Route::put('/{member}', 'update')->name('update');
        Route::delete('/{member}', 'destroy')->name('destroy');
        Route::get('/export/excel', 'exportExcel')->name('export.excel');
    });

    // 📰 Post Management
    Route::controller(PostController::class)->prefix('posts')->as('posts.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::patch('/{id}/toggle', 'toggle')->name('toggle');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::get('/{id}', 'show')->name('show');
    });

    // 📚 Document Management
    Route::controller(DocumentController::class)->prefix('documents')->as('documents.')->group(function () {
        Route::get('/', 'index')->name('index');
    });

    // 🕓 History Management
    Route::controller(HistoryController::class)->prefix('history')->as('history.')->group(function () {
        Route::get('/', 'index')->name('index');
    });

    // 💬 Comment Management
    Route::controller(CommentController::class)->prefix('comments')->as('comments.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{comment}', 'show')->name('show');
        Route::delete('/{comment}', 'destroy')->name('destroy');
        Route::post('/{comment}/toggle-status', 'toggleStatus')->name('toggleStatus');
    });

    // 🏛 Club Management
    Route::controller(ClubController::class)->prefix('clubs')->as('clubs.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::put('/{club}', 'update')->name('update');
        Route::delete('/{club}', 'destroy')->name('destroy');
        Route::post('/{club}/assign-manager', 'assignManager')->name('assignManager');
    });

    // 📝 Club Request Management
    Route::controller(ClubController::class)->prefix('club-requests')->as('club-requests.')->group(function () {
        Route::get('/', 'showRequests')->name('index');
        Route::post('/{clubRequest}', 'handleRequest')->name('handle');
    });

    // 🙋‍♂️ Club Join Request Management
    Route::controller(ClubController::class)->prefix('club-join-requests')->as('club-join-requests.')->group(function () {
        Route::get('/', 'showJoinRequests')->name('index');
        Route::post('/{clubJoinRequest}', 'handleJoinRequest')->name('handle');
    });

    // 🔔 Notification Management
    Route::controller(NotificationController::class)->prefix('notifications')->as('notifications.')->group(function () {
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
    });

    // 📊 Statistics Management
    Route::controller(StatisticsController::class)->prefix('stats')->as('stats.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/events', 'events')->name('events');
        Route::get('/clubs', 'clubs')->name('clubs');
        Route::get('/members', 'members')->name('members');
    });

    // 📋 Club Report Management
    Route::controller(ClubReportController::class)->prefix('clubs/{id}/report')->as('clubs.report.')->group(function () {
        Route::get('/', 'show')->name('show');
        Route::get('/pdf', 'exportPdf')->name('pdf');
    });

    // 📈 Statistics and Reports Management
    Route::controller(StatisticsController::class)->prefix('statistics-and-reports')->as('statistics-and-reports.')->group(function () {
        Route::get('/statistics', 'index')->name('statistics');
        Route::get('/reports', 'reports')->name('reports');
        
    });
});

// === Auth Routes ===
require __DIR__ . '/auth.php';