<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Notifications\DatabaseNotification;
use App\Observers\DatabaseNotificationObserver; // nhớ import observer
use Illuminate\Database\Eloquent\Relations\Relation;

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
        Paginator::useBootstrapFive();

        Relation::enforceMorphMap([
            'post' => 'App\Models\Post',
            'user' => 'App\Models\User',
            'events' =>'App\Models\Event',
        ]);

        // ✅ Gắn observer vào trong boot()
        DatabaseNotification::observe(DatabaseNotificationObserver::class);
    }
}
