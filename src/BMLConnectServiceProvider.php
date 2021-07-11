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
        $this->loadRoutesFrom(__DIR__.'/routes/bml_routes.php');
        
    }   

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('SHA443\BMLConnect\Http\Controllers\BMLConnectController');
        $this->mergeConfigFrom(__DIR__.'/config/bml.php','bml');
        
    }
}
