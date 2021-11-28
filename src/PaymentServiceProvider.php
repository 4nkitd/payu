<?php

namespace Dagar\PayU;

use Illuminate\Support\ServiceProvider;


class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerResources();
        $this->registerPublishing();

    }

    public function register()
    {
        $this->configure();
    }

    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/payu.php', 'payu'
        );
    }

    protected function registerResources()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'payu');
    }

    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/payu.php' => $this->app->configPath('payu.php'),
            ], 'payu-config');

            $this->publishes([
                __DIR__.'/../resources/views' => $this->app->resourcePath('views/dagar/payu'),
            ], 'payu-views');
        }
    }

}
