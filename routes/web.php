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
use App\Http\Middleware\CheckRole;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// -------------------
// Route test middleware role
// -------------------
Route::get('/test-role', function() {
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
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
        ->name('users.toggleStatus');

    // 📅 Event Management
    Route::resource('events', EventController::class);
    Route::post('events/{event}/approve', [EventController::class, 'approve'])->name('events.approve');
    Route::post('events/{event}/reject', [EventController::class, 'reject'])->name('events.reject');

    // 👨‍👩‍👧‍👦 Member Management
    Route::controller(MemberController::class)
        ->prefix('members')
        ->as('members.')
        ->group(function () {
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

Route::controller(PostController::class)
    ->prefix('posts')
    ->as('posts.')
    ->group(function () {
        // Trang danh sách bài viết
        Route::get('/', 'index')->name('index');

        // Lọc bài viết realtime (AJAX)
        Route::post('/filter', 'filter')->name('filter');

        // Ẩn / Hiện bài viết
        Route::patch('/{id}/toggle', 'toggle')->name('toggle');

        // Xóa bài viết
        Route::delete('/{id}', 'destroy')->name('destroy');

        // Xem chi tiết bài viết
        Route::get('/{id}', 'show')->name('show');
    });

    // 📚 Document Management
    Route::controller(DocumentController::class)
        ->prefix('documents')
        ->as('documents.')
        ->group(function () {
            // Trang danh sách tài liệu
            Route::get('/', 'index')->name('index');

            // API lọc tài liệu (AJAX)
            Route::post('/filter', 'filter')->name('filter');

        });

    // 🕓 History Management
    Route::controller(HistoryController::class)
        ->prefix('history')
        ->as('history.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
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
            Route::put('/{club}', 'update')->name('update');
            Route::delete('/{club}', 'destroy')->name('destroy');
            Route::post('/{club}/assign-manager', 'assignManager')->name('assignManager');
        });

    // 📝 Club Request Management
    Route::controller(ClubController::class)
        ->prefix('club-requests')
        ->as('club-requests.')
        ->group(function () {
            Route::get('/', 'showRequests')->name('index');
            Route::post('/{clubRequest}', 'handleRequest')->name('handle');
        });

    // 🙋‍♂️ Club Join Request Management
    Route::controller(ClubController::class)
        ->prefix('club-join-requests')
        ->as('club-join-requests.')
        ->group(function () {
            Route::get('/', 'showJoinRequests')->name('index');
            Route::post('/{clubJoinRequest}', 'handleJoinRequest')->name('handle');
        });

    // 🔔 Notification Management
    Route::controller(NotificationController::class)
        ->prefix('notifications')
        ->as('notifications.')
        ->group(function () {
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
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

    // Route test admin
    Route::get('/test-role', function () {
        return 'Bạn có quyền truy cập admin!';
    });
});

// === Auth Routes ===
require __DIR__ . '/auth.php';
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// === Public Routes ===
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

// === Authenticated User Routes ===
Route::middleware(['auth'])->group(function () {
    // Profile management
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});

// === Admin Routes ===
Route::prefix('admin')
    ->middleware(['auth'])
    ->as('admin.')
    ->group(function () {
        // User Management
        Route::controller(UserController::class)->group(function () {
            Route::resource('users', UserController::class);
            Route::post('users/{user}/toggle-status', 'toggleStatus')->name('users.toggleStatus');
        });

        // Event Management
        Route::controller(EventController::class)->group(function () {
            Route::resource('events', EventController::class);
        });

        // Member Management
        Route::controller(MemberController::class)->group(function () {
            Route::resource('members', MemberController::class);
            Route::get('members/export/excel', 'exportExcel')->name('members.export.excel');
        });

        // Post Management
        Route::prefix('posts')->as('posts.')->group(function () {
            Route::controller(PostController::class)->group(function () {
                Route::get('/', 'index')->name('index');
            });
        });

        // Document Management
        Route::prefix('documents')->as('documents.')->group(function () {
            Route::controller(DocumentController::class)->group(function () {
                Route::get('/', 'index')->name('index');
            });
        });

        // History Management
        Route::prefix('history')->as('history.')->group(function () {
            Route::controller(HistoryController::class)->group(function () {
                Route::get('/', 'index')->name('index');
            });
        });

        // Comment Management
        Route::prefix('comments')->as('comments.')->group(function () {
            Route::controller(CommentController::class)->group(function () {
                Route::get('/', 'index')->name('index');
            });
        });

        // Club Management
        Route::prefix('clubs')->as('clubs.')->group(function () {
            Route::controller(ClubController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::put('/{club}', 'update')->name('update');
                Route::delete('/{club}', 'destroy')->name('destroy');
                Route::post('/{club}/assign-manager', 'assignManager')->name('assignManager');
            });
        });

        // Club Request Management
        Route::prefix('club-requests')->as('club-requests.')->group(function () {
            Route::controller(ClubController::class)->group(function () {
                Route::get('/', 'showRequests')->name('index');
                Route::post('/{clubRequest}', 'handleRequest')->name('handle');
            });
        });

        // Club Join Request Management
        Route::prefix('club-join-requests')->as('club-join-requests.')->group(function () {
            Route::controller(ClubController::class)->group(function () {
                Route::get('/', 'showJoinRequests')->name('index');
                Route::post('/{clubJoinRequest}', 'handleJoinRequest')->name('handle');
            });
        });
        // Notification Management
          Route::controller(NotificationController::class)->group(function () {
              Route::get('/notifications/create', 'create')->name('notifications.create');
              Route::post('/notifications', 'store')->name('notifications.store');
          });
        // Statistics
      

    Route::controller(StatisticsController::class)->group(function () {
        // Trang tổng quan thống kê
        Route::get('/stats', 'index')->name('admin.stats');

        // Thống kê theo sự kiện
        Route::get('/stats/events', 'events')->name('admin.stats.events');

        // Thống kê theo câu lạc bộ
        Route::get('/stats/clubs', 'clubs')->name('admin.stats.clubs');

        // Nếu muốn thêm thống kê theo thành viên
        Route::get('/stats/members', 'members')->name('admin.stats.members');
 
});
  Route::get('/clubs/{id}/report', [ClubReportController::class, 'show'])->name('clubs.report');
// PDF Export for Club Report
  Route::get('/clubs/{id}/report/pdf', [ClubReportController::class, 'exportPdf'])
    ->name('clubs.report.pdf');
});

// === Authentication Routes ===
require __DIR__ . '/auth.php';
