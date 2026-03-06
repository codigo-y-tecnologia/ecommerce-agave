<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
            Setting::getValue('allow_order_returns', false)
        );

        View::share(
            'allowClaimeOrders',
            Setting::getValue('auto_register_guest_after_purchase', false)
        );
    }
}
