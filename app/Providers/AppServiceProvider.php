<?php

namespace App\Providers;

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
        // Safety net: validasi GPS tidak boleh dilewati di produksi,
        // apa pun nilai DEV_SKIP_GPS di .env.
        if ($this->app->isProduction()) {
            config(['app.skip_gps_validation' => false]);
        }
    }
}
