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
    protected $signature = 'run {container?}';

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
        $container = $this->argument('container');
        $this->cmd('docker-compose up -d '.$container);
        $this->info('Done.');
        $this->openProjectInBrowser($container ?? '');
    }

    /**
     * @param string $container
     * @throws \Exception
     */
    private function openProjectInBrowser(string $container = '')
    {
        if (!empty($container)) {
            return;
        }

        $this->cmd('source ../.env && sensible-browser $APP_URL');
    }
}
