<?php

namespace App\Platforms\Providers;

use App\Enums\StatusEnum;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\App;

class IOSPlatform extends \App\Platforms\Platform
{

    public static $handler = null ;

    /**
     * system check repeatedly status when get any error while check status.
     * this method get numbers of seconds for delay and re-check
     *
     * @return int number of seconds for delay and re-check
     */
    public function reCheckStatusOnErrorOccurred(): int
    {
        return 2 * 60 * 60;
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
        $config['base_uri'] = 'https://www.apple.com/app-store/';
        if (App::runningUnitTests() and self::$handler != null )
            $config['handler']  = self::$handler;
        $client = new Client($config);
        $options['Accept'] = 'application/json';
        $request = $client->request("GET",$appUID.'/subscription/check',$options);
        $body = $request->getBody()->getContents();
        $status = json_decode($body);
        return StatusEnum::getFromString($status->subscription);
    }
}
