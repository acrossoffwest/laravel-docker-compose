<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

abstract class RunCLICommandInDockerPath extends RunCLICommand
{
    protected function cmd($command): string
    {
        return system('cd '.$this->getAbsoulteDockerPath().' && '.$command);
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
}
