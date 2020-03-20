<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

abstract class RunCLICommandInDockerPath extends RunCLICommand
{
    /**
     * @param string $relativeDockerPath
     * @return string
     * @throws \Exception
     */
    public function getAbsolutePath(string $relativeDockerPath = 'docker'): string
    {
        $workingDir = parent::getAbsolutePath();
        $absolutePath = $workingDir.'/'.$relativeDockerPath;

        if (!is_dir($absolutePath)) {
            throw new \Exception('Docker directory: "'.$absolutePath.'" not found');
        }

        return $absolutePath;
    }
}
