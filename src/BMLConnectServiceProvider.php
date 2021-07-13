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
        $this->mergeConfigFrom(__DIR__.'/config/bml.php','bml');
    }
}
