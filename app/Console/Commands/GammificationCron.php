<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GammificationCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gammification:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command For Running Gammification Work In Background';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Artisan::call('queue:work > Storage/logs/jobs.log --tries=3 --stop-until-empty');
    }
}
