<?php

namespace Wame\LaravelAuth;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LaravelAuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/wame-auth.php', 'wame-auth');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'wame-auth');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // Export the migration
            $this->publishMigrations();

            // Export configs
            $this->publishConfigs();

            // Export views
            $this->publishViews();
        }

        $this->registerRoutes();
    }

    /**
     * @return void
     */
    protected function registerRoutes(): void
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }

    /**
     * @return array
     */
    protected function routeConfiguration(): array
    {
        return [
            'prefix' => 'api/v1'
        ];
    }

    /**
     * @return void
     */
    private function publishMigrations(): void
    {

        // Delete old user migration
        if (file_exists(database_path('/migrations/2014_10_12_000000_create_users_table.php'))) {
            unlink(database_path('/migrations/2014_10_12_000000_create_users_table.php'));
        }

        $migrations = [];

        // If user migrations are not published already
        if (!file_exists(database_path('/migrations/2023_01_17_094244_change_oauth_passport_column_types.php'))) {
            $migrations[ __DIR__ . '/../database/migrations/change_oauth_column_types.php.stub'] = database_path('migrations/2023_01_17_094244_change_oauth_passport_column_types.php');
        }

        $this->publishes($migrations, 'migrations');
    }

    /**
     * @return void
     */
    private function publishConfigs(): void
    {
        $this->publishes([
            __DIR__.'/../config/wame-auth.php' => config_path('wame-auth.php'),
        ], 'config');
    }

    /**
     * @return void
     */
    private function publishViews(): void
    {
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/wame-auth'),
        ], 'views');

    }
}
