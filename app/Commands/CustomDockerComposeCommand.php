<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class CustomDockerComposeCommand extends RunCLICommandInDockerPath
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'cmd {c}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Run custom docker-compose command from Laravel project';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     * @return mixed
     */
    public function handle()
    {
        $command = $this->argument('c');
        $this->cmd('docker-compose '.$command);
        $this->info('Done.');
    }
}
