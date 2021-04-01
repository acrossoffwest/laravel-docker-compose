<?php

namespace App\Commands;

class KillAllDockerComposeProjects extends RunAllDockerComposeProjects
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'kill:all';

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
            $this->info('Killing: "'.$dir.'"');
            try {
                $this->call('kill', [
                    '--project' => $absPath.$dir
                ]);
                $this->info('Killed: "'.$dir.'"');
            } catch (\Throwable $e) {
                $this->info('Failed killing: "'.$dir.'"');
            }
        }
    }
}
