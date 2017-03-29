<?php

namespace Gbowo\Bridge\Laravel;

use Closure;
use Gbowo\GbowoFactory;
use InvalidArgumentException;
use Illuminate\Contracts\Foundation\Application;

class GbowoManager
{
    protected static $supportedAdapters = [
        GbowoFactory::PAYSTACK => GbowoFactory::PAYSTACK,
        GbowoFactory::AMPLIFY_PAY => GbowoFactory::AMPLIFY_PAY
    ];

    protected $customAdapters = [];

    protected $adapters = [];

    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function adapter(string $name = null)
    {
        $adapter = $name ?? $this->getDefaultDriverName();

        return $this->adapters[$adapter] = $this->getAdapter($adapter);
    }

    protected function getDefaultDriverName()
    {
        return $this->app['config']['services.gbowo.default'];
    }

    protected function getAdapter(string $name)
    {
        return isset($this->adapters[$name]) ? $this->adapters[$name] : $this->resolveAdapter($name);
    }

    protected function resolveAdapter(string $name)
    {
        if (isset($this->customAdapters[$name])) {
            return $this->customAdapters[$name]();
        }

        if (!array_key_exists($name, self::$supportedAdapters)) {
            throw new InvalidArgumentException(
                "The specified adapter, {$name} is not supported"
            );
        }

        $method = "create" . ucfirst(self::$supportedAdapters[$name]) . "Adapter";

        return $this->{$method}();
    }

    public function createPaystackAdapter()
    {
        return $this->app->make("gbowo.paystack");
    }

    public function createAmplifyPayAdapter()
    {
        return $this->app->make("gbowo.amplifypay");
    }

    public function extend(string $adapterName, Closure $callback)
    {
        $this->customAdapters[$adapterName] = $callback;
    }
}
