<?php

namespace App\Modules\Membership\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class MembershipServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register module-specific bindings
        $this->app->bind(
            \App\Modules\Membership\Repositories\MembershipRepository::class,
            \App\Modules\Membership\Repositories\MembershipRepository::class
        );
        $this->app->singleton(
            \App\Modules\Membership\Services\PointService::class,
            \App\Modules\Membership\Services\PointService::class
        );
        $this->app->singleton(
            \App\Modules\Membership\Services\TierUpgradeService::class,
            \App\Modules\Membership\Services\TierUpgradeService::class
        );

        // Merge package configuration
        $this->mergeConfigFrom(
            __DIR__.'/../../../config/points.php', 'points'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        
        # Load module routes (if they were in module's folder, but here we explicitly use base_path in api.php)
        # However, for true modularity, you'd uncomment these and have specific module route files:
        // $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        // $this->loadRoutesFrom(__DIR__.'/../Routes/web.php'); // if module has web routes
    }
}
