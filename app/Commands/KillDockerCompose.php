<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class KillDockerCompose extends RunCLICommandInDockerPath
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'kill';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Kill docker-compose from Laravel project';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     * @return mixed
     */
    public function handle()
    {
        $this->cmd('docker-compose kill');
        $this->info('Done.');
    }
}
