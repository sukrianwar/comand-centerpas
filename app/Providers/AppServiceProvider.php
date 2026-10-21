<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
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
        // Super Admin selalu lolos semua pengecekan Gate/Policy.
        Gate::before(function ($user, string $ability) {
            return $user->isSuperAdmin() ? true : null;
        });
    }
}
