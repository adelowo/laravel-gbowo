# Laravel-Gbowo - A Laravel bridge for [Gbowo][gbowo]

[![Latest Version on Packagist](https://img.shields.io/packagist/v/adelowo/laravel-gbowo.svg?style=flat-square)](https://packagist.org/packages/adelowo/laravel-gbowo)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/adelowo/laravel-gbowo/master.svg?style=flat-square)](https://travis-ci.org/adelowo/laravel-gbowo)


- [Installation](#install)
- [Usage](#usage)
- [API Methods](#api)

### Installation

<div id="install"></div>

````bash

$ composer require adelowo/laravel-gbowo

````

- Append the `GbowoServiceProvider` to the `provider` key in `config/app.php`

```php
Gbowo\Bridge\Laravel\GbowoServiceProvider::class
```

- Set up a default adapter in `config/services.php`

````php
<?php
//...other config values
"gbowo" => [
    "default" => "paystack"
];
 
````

> This step is optionally but can be helpful when you are certain you wouldn't be switching adapters in multiple places in your app.

> Only ___paystack___ and ___amplifypay___ are currently supported.

- Optional, you can also make use of facades by adding this to the `aliases` key in `config/app.php`

```php

"Gbowo" : Gbowo\Bridge\Laravel\Facades::class

```


##### Usage

<div id="usage"></div>

> [Gbowo's][gbowo] official doc is highly recommendeded.

```php

class BillingController extends Controller
{
    public function chargeCustomer(Request $request)
    {
        $adapter = app("gbowo")->adapter("paystack");
        // $adapter = app("gbowo")->adapter("amplifypay");

        
        $data = ["email" => $request->get('email') , "amount" => $request->get('amount')];
        
        return redirect($adapter->charge($data));
    }
}

```

> Calling the `adapter` method without passing in an adapter name would return an instance of the default adapter set in the config file.

#### Adding custom adapters

If you have written a custom adapter, you can include this in your app by doing what is obtainable below in a `ServiceProvider` of your choice.

```php

$config = ["key" => "value", "other" => "stuff"]; //some bootstrap options your adapter might need.

$this->app["gbowo"]->extend("voguepay" , function() use ($config)){
    return new VoguePayAdapter($config);
});

```

And you can access this new adapter anywhere in your code via 

```php

$voguePay = app("gbowo")->adapter("voguePay");

$voguePay->charge(['c' => 'd']);

```


#### Facades

````php

use Gbowo\Bridge\Laravel\Facades

Gbowo::adapter("paystack")->charge([])

//You can call any method on the facade as you would in the normal instance. Do check the api methods
````

### API methods
<div id="api"> </div>

- `createPaystackAdapter()`
- `createAmplifyPayAdapter()`
- `extend(string $adapterName, Closure $callback)`
- `adapter(string $name = null)`
> If `$name` is left null, the default adapter would be fetched by inspecting the value of `config.services.gbowo.default`

### License
[MIT](http://opensource.org/licenses/MIT)

[gbowo]:https://github.com/adelowo/gbowo

