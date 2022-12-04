<?php

namespace App\Platforms;

abstract class Platform implements PlatformInterface
{
    protected $app;

    /**
     * system check repeatedly status when get any error while check status.
     * this method get numbers of seconds for delay and re-check
     *
     * @return int number of seconds for delay and re-check
     */
    public static function reCheckStatusOnErrorOccurred():int
    {
        return 0;
    }

    /**
     * set application that should be checked.
     * @param $app
     */
    public function setApp($app) : void {
        $this->app = $app;
    }
}
