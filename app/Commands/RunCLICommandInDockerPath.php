<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

abstract class RunCLICommandInDockerPath extends RunCLICommand
{
    protected $outputArray = [];

    /**
     * @param $command
     * @return string
     * @throws \Exception
     */
    protected function cmd($command): string
    {
        return exec('cd '.$this->getAbsoulteDockerPath().' && '.$command, $this->outputArray);
    }
}
