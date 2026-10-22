<?php

namespace App\Providers;

use App\Models\Area;
use App\Models\PatrolSchedule;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
        // Super Admin selalu lolos semua pengecekan Gate/Policy.
        Gate::before(function ($user, string $ability) {
            return $user->isSuperAdmin() ? true : null;
        });

        View::composer('components.impersonation-banner', function ($view) {
            $impersonatorId = session('impersonator_id');

            $view->with(
                'impersonator',
                $impersonatorId ? User::find($impersonatorId) : null
            );
        });

        View::composer('layouts.member', function ($view) {
            $areas = Area::orderBy('area_name')
                ->get(['id', 'area_name'])
                ->map(fn ($area) => [
                    'id' => $area->id,
                    'name' => $area->area_name,
                ])
                ->values()
                ->all();

            $suggestedAreaId = PatrolSchedule::activeNow()
                ->orderBy('start_time')
                ->value('area_id');

            $view->with([
                'reportAreas' => $areas,
                'suggestedAreaId' => $suggestedAreaId,
            ]);
        });
    }
}
