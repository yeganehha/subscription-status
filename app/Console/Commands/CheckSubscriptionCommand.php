<?php

namespace App\Console\Commands;

use App\Services\CheckService;
use Illuminate\Console\Command;

class CheckSubscriptionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check each application\'s subscribe status';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Throwable
     */
    public function handle()
    {
        CheckService::start();

        return Command::SUCCESS;
    }
}
