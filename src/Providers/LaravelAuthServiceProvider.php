<?php

declare(strict_types=1);

namespace Wame\LaravelAuth\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Wame\LaravelAuth\Console\InstallLaravelAuth;

class LaravelAuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // $this->mergeConfigFrom(__DIR__ . '/../../config/wame-auth.php', 'wame-auth');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'wame-auth');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        if ($this->app->runningInConsole()) {
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

        $this->registerVerificationMail();

        $this->registerRoutes();

        $this->registerTranslations();
    }

    protected function registerRoutes(): void
    {
        Route::group($this->routeConfiguration(), function (): void {
            $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        });
        // $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
    }

    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'laravel-auth');
    }

    protected function routeConfiguration(): array
    {
        return [
            'prefix' => config('wame-auth.route.prefix', 'api/v1'),
        ];
    }

    private function publishConfigs(): void
    {
        $this->publishes([
            __DIR__.'/../../config/wame-auth.php' => config_path('wame-auth.php'),
        ], 'config');
    }

    private function publishViews(): void
    {
        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/wame-auth'),
        ], 'views');
    }

    private function publishTranslations(): void
    {
        $this->publishes([
            __DIR__.'/../../resources/lang' => resource_path('lang/vendor/laravel-auth'),
        ], 'translations');
    }

    private function registerVerificationMail(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject(__('laravel-auth::emails.verification_link.subject'))
                ->markdown('wame-auth::emails.users.verification_link', [
                    'verificationLink' => $url,
                ]);
        });
    }
}
