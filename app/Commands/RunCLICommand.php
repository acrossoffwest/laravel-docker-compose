<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

abstract class RunCLICommand extends Command
{
    /**
     * Execute the console command.
     *
     * @throws \Exception
     * @return mixed
     */
    abstract public function handle();

    abstract protected function cmd($command): string;

    protected function runCLICommand(string $command): string
    {
        return system($command);
    }

    protected function getAbsoulteDockerPath(string $relativeDockerPath = 'docker'): string
    {
        $workingDir = getcwd();
        $absoluteDockerPath = $workingDir.'/'.$relativeDockerPath;

        if (!is_dir($absoluteDockerPath)) {
            throw new \Exception('Docker directory: "'.$absoluteDockerPath.'" not found');
        }

        return $absoluteDockerPath;
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
