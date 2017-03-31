<?php

namespace Gbowo\Bridge\Laravel\Tests;

use InvalidArgumentException;
use Gbowo\Adapter\Amplifypay\AmplifypayAdapter;
use Gbowo\Adapter\Paystack\PaystackAdapter;
use Gbowo\Bridge\Laravel\GbowoManager;
use Gbowo\Contract\Adapter\AdapterInterface;
use Gbowo\GbowoFactory;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophet;

class GbowoManagerTest extends TestCase
{

    /**
     * @var GbowoManager
     */
    protected $manager;

    /**
     * @var Prophet
     */
    protected $prophet;

    public function tearDown()
    {
        $this->prophet->checkPredictions();
    }

    public function setUp()
    {
        $this->prophet = new Prophet();

        $app = $this->prophet->prophesize(Application::class);

        $app->offsetGet("config")
            ->willReturn(new Repository(["services" => ["gbowo" => ["default" => "paystack"]]]));

        $app->make("gbowo.paystack")
            ->willReturn(new PaystackAdapter());

        $app->make("gbowo.amplifypay")
            ->willReturn(new AmplifypayAdapter());

        $this->manager = new GbowoManager($app->reveal());
    }

    /**
     * @dataProvider adapters
     */
    public function testAdapter(string $adapterName, string $adapter)
    {
        $this->assertInstanceOf($adapter, $this->manager->adapter($adapterName));
    }

    public function adapters()
    {
        return [
            [GbowoFactory::PAYSTACK, PaystackAdapter::class],
            [GbowoFactory::AMPLIFY_PAY, AmplifypayAdapter::class]
        ];
    }

    public function testDefaultDriverNameIsCorrectlyDetermined()
    {
        $method = new \ReflectionMethod($this->manager, "getDefaultDriverName");
        $method->setAccessible(true);

        $this->assertSame(GbowoFactory::PAYSTACK, $method->invoke($this->manager));
    }

    public function testFetchesTheDefaultAdapterImplementation()
    {
        //The setup method defines the default adapter as "paystack"
        $this->assertInstanceOf(PaystackAdapter::class, $this->manager->adapter());
    }

    public function testUnableToResolveUnknownAdapter()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->manager->adapter("interswitch");
    }

    public function testExtensibility()
    {
        $stripeAdapter = new class implements AdapterInterface
        {
            const ADAPTER_NAME = "stripe";

            public function charge(array $data = [])
            {
                return "Charged by " . ucfirst(self::ADAPTER_NAME);
            }
        };

        $this->manager->extend(
            "stripe",
            function () use ($stripeAdapter) {
                return $stripeAdapter;
            });

        $this->assertSame($stripeAdapter, $this->manager->adapter("stripe"));
    }

    public function testPaystackAdapter()
    {
        $this->assertInstanceOf(
            PaystackAdapter::class,
            $this->manager->createPaystackAdapter()
        );
    }

    public function testAmplifyPayAdapter()
    {
        $this->assertInstanceOf(
            AmplifypayAdapter::class,
            $this->manager->createAmplifyPayAdapter()
        );
    }
}
