<?php

namespace App\Platforms\Providers;

use App\Enums\StatusEnum;

class IOSPlatform extends \App\Platforms\Platform
{
    /**
     * system check repeatedly status when get any error while check status.
     * this method get numbers of seconds for delay and re-check
     *
     * @return int number of seconds for delay and re-check
     */
    public static function reCheckStatusOnErrorOccurred(): int
    {
        return parent::reCheckStatusOnErrorOccurred();
    }

    /**
     * Process to check status of app in platform.
     *
     * @return StatusEnum
     */
    public function getStatus(): StatusEnum
    {
        $appUID = $this->app->uid;
        return StatusEnum::Active;
    }
}
