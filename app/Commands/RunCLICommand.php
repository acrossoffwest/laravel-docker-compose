<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

abstract class RunCLICommand extends Command
{

    protected $absoluteBasepath = null;
    protected $absoluteDockerpath = null;
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

    protected function cmdInBasePath($command): string
    {
        return exec('cd '.$this->getAbsoulteBasepath().' && '.$command, $this->outputArray);
    }

    /**
     * @param string $relativeDockerPath
     * @return string
     * @throws \Exception
     */
    protected function getAbsoulteDockerPath(string $relativeDockerPath = 'docker'): string
    {
        if (!empty($this->absoluteDockerpath)) {
            return $this->absoluteDockerpath;
        }

        $workingDir = $this->getAbsoulteBasepath();
        $absoluteDockerPath = $workingDir.'/'.$relativeDockerPath;

        if (!is_dir($absoluteDockerPath)) {
            throw new \Exception('Docker directory: "'.$absoluteDockerPath.'" not found');
        }

        return $this->absoluteDockerpath = $absoluteDockerPath;
    }

    protected function getAbsoulteBasepath(): string
    {
        if (!empty($this->absoluteBasepath)) {
            return $this->absoluteBasepath;
        }

        return $this->absoluteBasepath = getcwd();
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
