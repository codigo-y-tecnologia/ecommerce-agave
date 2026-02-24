<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Auth\Events\Login;
use App\Listeners\MergeGuestCartOnLogin;
use Illuminate\Auth\Events\Verified;
use App\Listeners\MergeGuestOrdersOnEmailVerified;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class => [
            MergeGuestCartOnLogin::class,
        ],

        Verified::class => [
            MergeGuestOrdersOnEmailVerified::class,
        ],
    ];
}
