<?php

namespace Gbowo\Bridge\Laravel;

use Illuminate\Support\ServiceProvider;
use Gbowo\Adapter\Paystack\PaystackAdapter;
use Gbowo\Adapter\Amplifypay\AmplifypayAdapter;

class GbowoServiceProvider extends ServiceProvider
{

    protected $defer = true;

    public function register()
    {
        $this->registerManager();
        $this->registerAdapters();
    }

    protected function registerManager()
    {
        $this->app->singleton('gbowo', function ($app) {
            return new GbowoManager($app);
        });
    }

    protected function registerAdapters()
    {
        $this->app->bind("gbowo.paystack", function () {
            return new PaystackAdapter();
        });

        $this->app->bind("gbowo.amplifypay", function () {
            return new AmplifypayAdapter();
        });

    }

    public function provides()
    {
        return ["gbowo", "gbowo.paystack", "gbowo.amplifypay"];
    }
}
