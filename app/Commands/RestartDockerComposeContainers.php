<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class RestartDockerComposeContainers extends RunCLICommandInDockerPath
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'restart {container?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Restart docker-compose containers from Laravel project';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     * @return mixed
     */
    public function handle()
    {
        $args = $this->arguments();

        $this->call('kill', $args);
        $this->call('run', $args);

        $this->info('Done.');
    }
}
