<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class GitPullCurrentBranchInProject extends RunCLICommandInDockerPath
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'git:pull {--project= : Project path with docker settings}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Git pull current branch and restart docker-compose containers from Laravel project';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     * @return mixed
     */
    public function handle()
    {
        $this->absolutePath = $this->option('project') ?? $this->getAbsolutePath('');

        $this->cmd('git pull origin $(git rev-parse --abbrev-ref HEAD)');
        $this->call('restart', [
            '--project' => $this->absolutePath
        ]);
    }
}
