<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class RunAllProjects extends RunCLICommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'run:all';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Run all projects in current directory';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dir = $this->getPathWithAbsolutePath('./');

        $dirs = array_filter(scandir($dir), function ($dir) {
            return !in_array($dir, ['.', '..']);
        });

        array_map(function ($dir) {
            if (!is_dir($this->getPathWithAbsolutePath($dir.'/docker'))) {
                return;
            }
            $this->cmd([
                'cd '.$dir,
                'ldc run'
            ]);
        }, $dirs);
    }
}
