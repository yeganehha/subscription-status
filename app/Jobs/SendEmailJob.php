<?php

namespace App\Jobs;

use App\Mail\ExpiredMail;
use App\Models\Run;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array|null $config ;
    public Collection $apps_id ;
    public Run $run ;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Collection $apps_id , Run $run , array|null $config = null)
    {
        $this->config = $config;
        $this->apps_id = $apps_id;
        $this->run = $run;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->config['email']['to'])->send(new ExpiredMail($this->apps_id,$this->config));
        $this->run->finish();
    }
}
