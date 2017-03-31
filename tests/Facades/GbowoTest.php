<?php

namespace Gbowo\Bridge\Laravel\Tests;

use Gbowo\Bridge\Laravel\Facades\Gbowo;
use Gbowo\Bridge\Laravel\GbowoManager;
use Illuminate\Foundation\Application;
use PHPUnit\Framework\TestCase;

class GbowoTest extends TestCase
{

    public function setUp()
    {
        $app = $this->prophesize(Application::class);

        $app->offsetGet("gbowo")
            ->willReturn(new GbowoManager($app->reveal()));

        Gbowo::setFacadeApplication($app->reveal());
    }

    public function testFacadesWorksAsExpected()
    {
        $this->assertInstanceOf(
            GbowoManager::class,
            Gbowo::getFacadeRoot()
        );
    }
}
