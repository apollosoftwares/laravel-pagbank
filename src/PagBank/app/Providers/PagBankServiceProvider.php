<?php

namespace Apollosoftwares\Pagbank\App\Providers;

use Illuminate\Support\ServiceProvider;

use Apollosoftwares\Pagbank\PagBank;

class PagBankServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('pagbank', function ($app) {
            return new PagBank($app['log'], $app['validator']);
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/pagbank.php' => config_path('pagbank.php'),
        ], 'config');
    }
}
