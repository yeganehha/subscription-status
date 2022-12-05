<?php

namespace App\Jobs;

use App\Models\App;
use App\Models\Run;
use App\Services\CheckService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckApplicationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected App $app;
    protected Run $run;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(App $app ,Run $run )
    {
        $this->run = $run;
        $this->app = $app;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Throwable
     */
    public function handle()
    {
        CheckService::check($this->app,$this->run);
    }
}
