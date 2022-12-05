<?php

namespace Tests;

use App\Platforms\Providers\AndroidPlatform;
use App\Platforms\Providers\IOSPlatform;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Yeganehha\DigikalaSellerWebhook\APIHandler;
use Yeganehha\DigikalaSellerWebhook\Tests\PHPUnitUtil;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp():void
    {
        parent::setUp();
        DB::beginTransaction();
    }

    protected function tearDown():void
    {
        DB::rollback();
        parent::tearDown();
    }

    public static function HttpSuccessMockHandler($status = "active"){
        $mock = new MockHandler([
            new Response(200,[], json_encode(['subscription' => $status])),
        ]);
        $handlerStack = HandlerStack::create($mock);
        IOSPlatform::$handler = $handlerStack;
        $mock = new MockHandler([
            new Response(200,[], json_encode(compact('status'))),
        ]);
        $handlerStack = HandlerStack::create($mock);
        AndroidPlatform::$handler = $handlerStack;
    }

    public static function HttpFaultMockHandler($status = "active"){
        $mock = new MockHandler([
            new Response(500,[], json_encode(['subscription' => $status])),
        ]);
        $handlerStack = HandlerStack::create($mock);
        IOSPlatform::$handler = $handlerStack;
        $mock = new MockHandler([
            new Response(500,[], json_encode(compact('status'))),
        ]);
        $handlerStack = HandlerStack::create($mock);
        AndroidPlatform::$handler = $handlerStack;
    }
}
