<?php

namespace App\Commands;

use Dotenv\Dotenv;
use Illuminate\Console\Scheduling\Schedule;
use JetBrains\PhpStorm\ArrayShape;
use LaravelZero\Framework\Commands\Command;

class RunAllDockerComposeProjects extends RunCLICommandInDockerPath
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
    protected $description = 'Run all docker-compose projects from sub dirs';

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
            $this->info('Running: "'.$dir.'"');
            try {
                $this->call('run', [
                    '--project' => $absPath.$dir
                ]);
                $this->info('Runned: "'.$dir.'"');
            } catch (\Throwable $e) {
                $this->info('Failed running: "'.$dir.'"');
            }
        }
    }

    /**
     * @param string $absPath
     * @return string[]
     */
    protected function getSubDirsWithDocker(string $absPath): array
    {
        return array_filter(
            scandir($absPath, GLOB_ONLYDIR),
            fn ($item) => !in_array($item, ['.', '..']) && !preg_match('/^\.(.+)*/', $item) && is_dir($absPath.$item) && is_dir($absPath.$item.'/docker')
        );
    }
}
