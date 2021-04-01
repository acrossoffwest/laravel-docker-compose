<?php

namespace App\Commands;

class RestartAllDockerComposeProjects extends RunAllDockerComposeProjects
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'restart:all';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Kill all docker-compose projects in sub dirs';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     * @return mixed
     */
    public function handle()
    {
        $absPath = $this->getAbsolutePath('').'/';
        $subDirs = $this->getSubDirsWithDocker($absPath);

        foreach ($subDirs as $dir) {
            $this->info('Restarting: "'.$dir.'"');
            try {
                $this->call('restart', [
                    '--project' => $absPath.$dir
                ]);
                $this->info('Restarted: "'.$dir.'"');
            } catch (\Throwable $e) {
                $this->info('Failed restarting: "'.$dir.'"');
            }
        }
    }
}
