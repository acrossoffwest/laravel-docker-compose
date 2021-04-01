<?php

namespace App\Commands;

class KillDockerCompose extends RunCLICommandInDockerPath
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'kill {container?} {--project= : Project path with docker settings}';

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
        $this->absolutePath = $this->option('project') ?? '';
        $this->cmd('docker-compose kill '.$this->argument('container'));
        $this->info('Done.');
    }
}
