<?php

namespace App\Platforms;

use App\Models\App;

abstract class Platform implements PlatformInterface
{
    protected App $app;

    /**
     * system check repeatedly status when get any error while check status.
     * this method get numbers of seconds for delay and re-check
     *
     * @return int number of seconds for delay and re-check
     */
    public function reCheckStatusOnErrorOccurred():int
    {
        return 0;
    }

    /**
     * set application that should be checked.
     * @param App $app
     */
    public function setApp(App $app) : void {
        $this->app = $app;
    }
}
