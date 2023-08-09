<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\PWServer\PWServer;
use App\PWServer\PWssh;

class PWServerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->bind('pwserver', function() {
            return new PWServer();
        });
        //
        $this->app->bind('pwssh', function() {
            return new PWssh();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
