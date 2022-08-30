<?php

namespace Paythem\Ptn;

use Illuminate\Support\ServiceProvider;

class PaythemServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Paythem\Ptn\PTNAPI');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
