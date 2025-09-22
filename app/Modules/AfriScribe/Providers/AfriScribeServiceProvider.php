<?php

namespace App\Modules\AfriScribe\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AfriScribeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');

        // Load migrations (migrations are in the main database/migrations directory)
        // $this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');

        // Load views
        $this->loadViewsFrom(resource_path('views'), 'afriscribe');

        // Publish configuration
        $this->publishes([
            __DIR__ . '/../Config/afriscribe.php' => config_path('afriscribe.php'),
        ], 'afriscribe-config');

        // Publish assets
        $this->publishes([
            __DIR__ . '/../Resources/assets' => public_path('vendor/afriscribe'),
        ], 'afriscribe-assets');
    }
}
