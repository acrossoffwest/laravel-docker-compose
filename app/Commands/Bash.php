<?php

namespace App\Commands;

class Bash extends RunCLICommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'bash {--container= : Container name} {--filter= : Filter}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Run bash in container';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     * @return mixed
     */
    public function handle()
    {
        $grep = $this->option('filter');
        $containerName = $this->option('container');
        $this->cmd('docker ps --format \'{{.Names}}\' '.(!empty($grep) ? ' | grep '.$grep : ''));
        if (!empty($containerName)) {
            if (!in_array($containerName, $this->outputArray)) {
                throw new \Exception('Container "'.$containerName.'" not found.');
            }
            $this->runDockerExec($containerName);
            return;
        }
        $option = $this->menu('Choose container', $this->outputArray)->open();

        if (empty($option)) {
            return;
        }

        $this->runDockerExec($this->outputArray[$option]);
    }

    protected function runDockerExec(string $containerName)
    {
        $command = 'docker exec -e TERM=xterm -i '.$containerName.' bash';
        $this->info('Run command: '.$command);
        $this->warn('You logged into: '.$containerName);
        $this->warn('Now you can enter your commands.');

        system($command);
        $this->info('Good bye.');
    }
}
