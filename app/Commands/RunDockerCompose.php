<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class RunDockerCompose extends RunCLICommandInDockerPath
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'run';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Run docker-compose from Laravel project';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     * @return mixed
     */
    public function handle()
    {
        $this->cmd('docker-compose up -d');
        $this->info('Done.');
        $this->cmdInBasePath('source .env && sensible-browser $APP_URL');
    }
}
