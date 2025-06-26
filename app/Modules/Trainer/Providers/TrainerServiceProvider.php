<?php

namespace App\Modules\Trainer\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TrainerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Modules\Trainer\Repositories\TrainerRepository::class,
            \App\Modules\Trainer\Repositories\TrainerRepository::class
        );
        $this->app->singleton(
            \App\Modules\Trainer\Services\TrainerService::class,
            \App\Modules\Trainer\Services\TrainerService::class
        );

        // Merge package configuration
        $this->mergeConfigFrom(
            __DIR__.'/../../../config/salary.php', 'salary'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        
        # Load module routes (if they were in module's folder, but here we explicitly use base_path in api.php)
        // $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
    }
}
