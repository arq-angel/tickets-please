<?php

namespace App\Providers;

use App\Exceptions\Handler;
use App\Models\Ticket;
use App\Models\User;
use App\Policies\Api\V1\TicketPolicy;
use App\Policies\Api\V1\UserPolicy;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
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
        Schema::defaultStringLength(248);

        // Register the policies
        Gate::policy(Ticket::class, TicketPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
