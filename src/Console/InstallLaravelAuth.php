<?php

namespace Wame\LaravelAuth\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallLaravelAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wame:auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Laravel Auth';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Artisan::call('vendor:publish', ['--tag' => 'passport-config'],);
        Artisan::call('vendor:publish', ['--tag' => 'eloquent-sortable-config']);
        Artisan::call('vendor:publish', ['--provider' => 'Wame\LaravelAuth\LaravelAuthServiceProvider', '--tag' => 'config',]);
        Artisan::call('vendor:publish', ['--provider' => 'Wame\LaravelAuth\LaravelAuthServiceProvider', '--tag' => 'migrations',]);
        Artisan::call('vendor:publish', ['--provider' => 'Spatie\Activitylog\ActivitylogServiceProvider', '--tag' => 'activitylog-migrations',]);

        return Command::SUCCESS;
    }
}
