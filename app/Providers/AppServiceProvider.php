<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Notifications\DatabaseNotification;
use App\Observers\DatabaseNotificationObserver; // nhớ import observer
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\FundTransaction;
use App\Observers\FundTransactionObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FundTransaction::observe(FundTransactionObserver::class);
        Paginator::useBootstrapFive();

        Relation::enforceMorphMap([
            'post' => 'App\Models\Post',
            'user' => 'App\Models\User',
            'events' =>'App\Models\Event',
        ]);

        // ✅ Gắn observer vào trong boot()
        DatabaseNotification::observe(DatabaseNotificationObserver::class);

        View::composer('admin.layouts.header', function ($view) {
            $user = Auth::user();
            $notifications = collect();
            $unreadCount = 0;

            if ($user) {
                $notifications = $user->notifications()
                    ->latest()
                    ->limit(10)
                    ->get();
                $unreadCount = $user->unreadNotifications()->count();
            }

            $view->with([
                'headerNotifications' => $notifications,
                'headerUnreadCount' => $unreadCount,
            ]);
        });
    }
}
