<?php

namespace App\Platforms;

use App\Enums\StatusEnum;

interface PlatformInterface
{
    /**
     * system check repeatedly status when get any error while check status.
     * this method get numbers of seconds for delay and re-check
     *
     * @return int number of seconds for delay and re-check
     */
    public static function reCheckStatusOnErrorOccurred():int;

    /**
     * Process to check status of app in platform.
     *
     * @return StatusEnum
     */
    public function getStatus() : StatusEnum ;


    /**
     * set application that should be checked.
     * @param $app
     */
    public function setApp($app) : void;
}
