<?php

namespace App\Providers;

use App\Service\CRM\Amo\AmoCRM;
use App\Service\CRM\ICRM;
use App\Service\Logger\FileLogService;
use App\Service\Logger\ILogger;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ILogger::class, function() {
            return new FileLogService();
        });
        $this->app->singleton(ICRM::class, function(Application $app) {
            return new AmoCRM($app->make(ILogger::class));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
