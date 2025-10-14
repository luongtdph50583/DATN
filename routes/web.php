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
    
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Nơi định nghĩa tất cả route của ứng dụng
|--------------------------------------------------------------------------
*/

// === 🏠 Public Routes ===
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

// === 👤 Authenticated User Routes ===
Route::middleware(['auth'])->group(function () {
    // Profile Management
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});

// === 🔐 Admin Routes ===
Route::prefix('admin')
    ->middleware(['auth'])
    ->as('admin.')
    ->group(function () {

        // 👥 User Management
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');

        // 📅 Event Management
        Route::resource('events', EventController::class);
        Route::post('events/{event}/approve', [EventController::class, 'approve'])->name('events.approve');
        Route::post('events/{event}/reject', [EventController::class, 'reject'])->name('events.reject');

        // 👨‍👩‍👧‍👦 Member Management
        Route::get('members', [MemberController::class, 'index'])->name('members.index');
        Route::post('members/{member}/toggle-status', [MemberController::class, 'toggleStatus'])->name('members.toggleStatus');
        Route::get('members/create', [MemberController::class, 'create'])->name('members.create');
        Route::post('members', [MemberController::class, 'store'])->name('members.store');
        Route::get('members/{member}', [MemberController::class, 'show'])->name('members.show');
        Route::get('members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit');
        Route::put('members/{member}', [MemberController::class, 'update'])->name('members.update');
        Route::delete('members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
        Route::get('members/export/excel', [MemberController::class, 'exportExcel'])->name('members.export.excel');

        // 📰 Post Management
        Route::prefix('posts')->as('posts.')->group(function () {
            Route::get('/', [PostController::class, 'index'])->name('index');
            Route::patch('/{id}/toggle', [PostController::class, 'toggle'])->name('toggle');
            Route::delete('/{id}', [PostController::class, 'destroy'])->name('destroy');
            Route::get('/{id}', [PostController::class, 'show'])->name('show');
        });

        // 📚 Document Management
        Route::get('documents', [DocumentController::class, 'index'])->name('documents.index');

        // 🕓 History Management
        Route::get('history', [HistoryController::class, 'index'])->name('history.index');

        // 💬 Comment Management
        Route::get('comments', [CommentController::class, 'index'])->name('comments.index');

        // 🏛 Club Management
        Route::prefix('clubs')->as('clubs.')->group(function () {
            Route::get('/', [ClubController::class, 'index'])->name('index');
            Route::get('/create', [ClubController::class, 'create'])->name('create');
            Route::post('/', [ClubController::class, 'store'])->name('store');
            Route::put('/{club}', [ClubController::class, 'update'])->name('update');
            Route::delete('/{club}', [ClubController::class, 'destroy'])->name('destroy');
            Route::post('/{club}/assign-manager', [ClubController::class, 'assignManager'])->name('assignManager');
        });

        // 📝 Club Request Management
        Route::prefix('club-requests')->as('club-requests.')->group(function () {
            Route::get('/', [ClubController::class, 'showRequests'])->name('index');
            Route::post('/{clubRequest}', [ClubController::class, 'handleRequest'])->name('handle');
        });

        // 🙋‍♂️ Club Join Request Management
        Route::prefix('club-join-requests')->as('club-join-requests.')->group(function () {
            Route::get('/', [ClubController::class, 'showJoinRequests'])->name('index');
            Route::post('/{clubJoinRequest}', [ClubController::class, 'handleJoinRequest'])->name('handle');
        });

        // 🔔 Notification Management
        Route::get('/notifications/create', [NotificationController::class, 'create'])->name('notifications.create');
        Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');
    });

// === Auth Routes (Laravel Breeze / Jetstream / Fortify etc.) ===
require __DIR__ . '/auth.php';
