<?php

namespace SHA443\BMLConnect;

use Illuminate\Support\ServiceProvider;

class BMLConnectServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }   

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // $this->app->make('SHA443\BMLConnect\Controllers\BMLConnectController');
        $this->mergeConfigFrom(__DIR__.'/config/bml.php','bml');
        
    }
}
