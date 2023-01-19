<?php

namespace Wame\LaravelAuth;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Wame\LaravelAuth\Console\InstallLaravelAuth;

class LaravelAuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/wame-auth.php', 'wame-auth');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'wame-auth');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {

            // Export the migration
            $this->publishMigrations();

            // Export configs
            $this->publishConfigs();

            // Export views
            $this->publishViews();

            // Export translations
            $this->publishTranslations();

            // Register Commands
            $this->commands([
                InstallLaravelAuth::class,
            ]);
        }

        $this->registerRoutes();
        $this->registerTranslations();
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
     * @return void
     */
    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'wame-auth');
    }

    /**
     * @return array
     */
    protected function routeConfiguration(): array
    {
        return [
            'prefix' => config('wame-auth.route.prefix', 'api/v1')
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
        if (file_exists(database_path('/migrations/2014_10_12_100000_create_password_resets_table.php'))) {
            unlink(database_path('/migrations/2014_10_12_100000_create_password_resets_table.php'));
        }

        $migrations = [];

        // If user migrations are not published already
        if (!file_exists(database_path('/migrations/2023_01_17_094244_change_oauth_passport_column_types.php'))) {
            $migrations[ __DIR__ . '/../database/migrations/change_oauth_column_types.php.stub'] = database_path('migrations/2023_01_17_094244_change_oauth_passport_column_types.php');
        }
        if (!file_exists(database_path('/migrations/2023_01_17_135603_create_user_password_resets_table.php'))) {
            $migrations[ __DIR__ . '/../database/migrations/create_user_password_resets_table.php.stub'] = database_path('migrations/2023_01_17_135603_create_user_password_resets_table.php');
        }
        if (!file_exists(database_path('/migrations/2023_01_17_135603_create_users__table.php'))) {
            $migrations[ __DIR__ . '/../database/migrations/create_users_table.php.stub'] = database_path('migrations/2023_01_17_135603_create_users__table.php');
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

    /**
     * @return void
     */
    private function publishTranslations(): void
    {
        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-auth'),
        ], 'translations');
    }
}
