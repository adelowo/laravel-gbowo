<?php

namespace Gbowo\Bridge\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Gbowo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'gbowo';
    }
}
