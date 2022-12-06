<?php

namespace App\Platforms\Providers;

use App\Enums\StatusEnum;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\App;

class AndroidPlatform extends \App\Platforms\Platform
{
    public static $handler = null ;

    /**
     * system check repeatedly status when get any error while check status.
     * this method get numbers of seconds for delay and re-check
     *Platforms\Providers
     * @return int number of seconds for delay and re-check
     */
    public function reCheckStatusOnErrorOccurred(): int
    {
        return 1 * 60 * 60;
    }

    /**
     * Process to check status of app in platform.
     *
     * @return StatusEnum
     * @throws GuzzleException
     */
    public function getStatus(): StatusEnum
    {
        $appUID = $this->app->uid;
        $config['base_uri'] = 'https://play.google.com/subscription/';
        if (App::runningUnitTests() and self::$handler != null )
            $config['handler'] = self::$handler;
        elseif ( config('app.debug' , false) )
        {
            switch( rand(1,3)) {
                case 1:
                    $status = "active";
                    break;
                case 2:
                    $status = "pending";
                    break;
                default:
                    $status = "expired";
                    break;
            }
            $httpCode = rand(1,3) != 1 ? 200 : 500;
            $mock = new MockHandler([
                new Response($httpCode,[], json_encode(compact('status'))),
            ]);
            $handlerStack = HandlerStack::create($mock);
            $config['handler']  = $handlerStack;
        }
        $client = new Client($config);
        $options['Accept'] = 'application/json';
        $request = $client->request("GET",$appUID.'/check',$options);
        $body = $request->getBody()->getContents();
        $status = json_decode($body);
        return StatusEnum::getFromString($status->status);
    }
}
