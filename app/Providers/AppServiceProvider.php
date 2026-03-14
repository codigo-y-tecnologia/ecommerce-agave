<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use App\Models\Setting;

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
        Gate::define('asignar_roles', function ($user) {
            return $user->hasRole('superadmin');
        });

        Paginator::useBootstrap();

        View::share(
            'allowOrderReturns',
            setting('allow_order_returns', 0)
        );

        View::share(
            'allowClaimeOrders',
            setting('auto_register_guest_after_purchase', 0)
        );

        Schema::defaultStringLength(191);
    }
}
