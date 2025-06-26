<?php

namespace App\Modules\Course\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CourseServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Modules\Course\Repositories\CourseRepository::class,
            \App\Modules\Course\Repositories\CourseRepository::class
        );
        $this->app->singleton(
            \App\Modules\Course\Services\CourseService::class,
            \App\Modules\Course\Services\CourseService::class
        );

        // Merge package configuration
        $this->mergeConfigFrom(
            __DIR__.'/../../../config/course.php', 'course'
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
