<?php

namespace App\Providers;

use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer(['components.header', 'components.notifications-dropdown'], function ($view) {
            if (!Auth::check()) {
                $view->with([
                    'notifications' => collect(),
                    'unreadNotificationCount' => 0,
                ]);

                return;
            }

            $notifications = UserNotification::query()
                ->where('user_id', Auth::id())
                ->latest()
                ->limit(15)
                ->get();

            $unreadNotificationCount = UserNotification::query()
                ->where('user_id', Auth::id())
                ->whereNull('read_at')
                ->count();

            $view->with(compact('notifications', 'unreadNotificationCount'));
        });
    }
}

