<?php

use App\Http\Controllers\Admin\{
    UserController,
    EventController,
    MemberController,
    PostController,
    DocumentController,
    HistoryController,
    CommentController,
    ClubController
};
use App\Http\Controllers\{
    HomeController,
    ProfileController
};
use App\Http\Controllers\Admin\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// === Public Routes ===
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

<<<<<<< HEAD
// Nhóm route dành cho admin với tiền tố /admin và middleware auth (tạm bỏ role)
Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Quản lý người dùng
    Route::resource('users', UserController::class)->names('admin.users');
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggleStatus');

    // Quản lý sự kiện
    Route::resource('events', EventController::class)->names('admin.events');

    // Quản lý thành viên
    Route::get('/members', [MemberController::class, 'index'])->name('admin.members.index');
    Route::get('/members/export/excel', [MemberController::class, 'exportExcel'])->name('admin.members.export.excel');

    // Tin tức & Bài viết
    Route::get('/posts', [PostController::class, 'index'])->name('admin.posts.index');
    Route::patch('/posts/{id}/toggle', [PostController::class, 'toggle'])->name('admin.posts.toggle');
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('admin.posts.destroy');
    Route::get('/posts/{id}', [PostController::class, 'show'])->name('admin.posts.show');

    // Tài liệu
    Route::get('/documents', [DocumentController::class, 'index'])->name('admin.documents.index');

    // Lịch sử tham gia
    Route::get('/history', [HistoryController::class, 'index'])->name('admin.history.index');

    // Bình luận
    Route::get('/comments', [CommentController::class, 'index'])->name('admin.comments.index');

    // Quản lý CLB
    Route::prefix('clubs')->group(function () {
        Route::get('/', [ClubController::class, 'index'])->name('admin.clubs.index');
        Route::get('/create', [ClubController::class, 'create'])->name('admin.clubs.create');
        Route::post('/', [ClubController::class, 'store'])->name('admin.clubs.store');
        Route::put('/{club}', [ClubController::class, 'update'])->name('admin.clubs.update');
        Route::delete('/{club}', [ClubController::class, 'destroy'])->name('admin.clubs.destroy');
        Route::post('/{club}/assign-manager', [ClubController::class, 'assignManager'])->name('admin.clubs.assignManager');
    });

    // Quản lý yêu cầu thành lập CLB
    Route::prefix('club-requests')->group(function () {
        Route::get('/', [ClubController::class, 'showRequests'])->name('admin.club-requests.index');
        Route::post('/{clubRequest}', [ClubController::class, 'handleRequest'])->name('admin.club-requests.handle');
    });

    // Quản lý yêu cầu tham gia CLB
    Route::prefix('club-join-requests')->group(function () {
        Route::get('/', [ClubController::class, 'showJoinRequests'])->name('admin.club-join-requests.index');
        Route::post('/{clubJoinRequest}', [ClubController::class, 'handleJoinRequest'])->name('admin.club-join-requests.handle');
=======
// === Authenticated User Routes ===
Route::middleware(['auth'])->group(function () {
    // Profile management
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
>>>>>>> 675c4230ea64f1035d7bdbe4c4f0ea59d095342f
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

<<<<<<< HEAD
// Include các route authentication
require __DIR__.'/auth.php';
=======
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
    });

// === Authentication Routes ===
require __DIR__ . '/auth.php';
>>>>>>> 675c4230ea64f1035d7bdbe4c4f0ea59d095342f
