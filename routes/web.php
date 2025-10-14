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
        Route::get('/{club}/edit', 'edit')->name('edit');        // <-- thêm
        Route::put('/{club}', 'update')->name('update');
        Route::delete('/{club}', 'destroy')->name('destroy');
        Route::post('/{club}/assign-manager', 'assignManager')->name('assignManager');
    });
});
   // 👉 Gán chủ nhiệm - hiển thị form
    Route::get('/clubs/{club}/assign-manager', [ClubController::class, 'showAssignForm'])
        ->name('clubs.showAssignForm');

    // 👉 Gán chủ nhiệm - xử lý form
    Route::post('/clubs/{club}/assign-manager', [ClubController::class, 'assignManager'])
        ->name('clubs.assignManager');


 Route::get('/club-requests', [ClubController::class, 'showRequests'])->name('club-requests.index');
    Route::post('/club-requests/{clubRequest}/handle', [ClubController::class, 'handleRequest'])->name('club-requests.handle');

    // Yêu cầu tham gia CLB
    Route::get('/club-join-requests', [ClubController::class, 'showJoinRequests'])->name('club-join-requests.index');
    Route::post('/club-join-requests/{clubJoinRequest}/handle', [ClubController::class, 'handleJoinRequest'])->name('club-join-requests.handle');
    });

// === Authentication Routes ===
require __DIR__ . '/auth.php';