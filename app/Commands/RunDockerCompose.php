<?php

namespace App\Commands;

use Dotenv\Dotenv;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class RunDockerCompose extends RunCLICommandInDockerPath
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'run {container?} {--browser : Open sensible browser}';

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
        if (!$this->option('browser')) {
            return;
        }
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

        $dotenv = Dotenv::createImmutable($this->getAbsolutePath('./'));
        $dotenv->load();

        $this->cmd('screen -d -m sensible-browser '.getenv('APP_URL'));
    }
}
