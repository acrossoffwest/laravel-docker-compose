<?php

namespace App\Commands;

class GitPullAndRestartAllDockerComposeProjects extends RunAllDockerComposeProjects
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'git:pull:all';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Git pull current branch and restart all docker-compose projects in sub dirs';

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
            $this->info('Git pull and restart: "'.$dir.'"');
            try {
                $this->call('git:pull', [
                    '--project' => $absPath.$dir
                ]);
                $this->info('Git pull and restart done: "'.$dir.'"');
            } catch (\Throwable $e) {
                $this->info('Failed git pull and restart: "'.$dir.'"');
            }
        }
    }
}
