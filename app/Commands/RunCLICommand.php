<?php

namespace App\Commands;

use App\Commands\Contracts\RunCLICommandContract;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

abstract class RunCLICommand extends Command implements RunCLICommandContract
{
    protected array $outputArray = [];
    protected string $absolutePath = '';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     * @return mixed
     */
    abstract public function handle();

    /**
     * @param array|string $command
     * @return string
     * @throws \Exception
     */
    public function cmd($command): string
    {
        $commands = [
            $this->cd()
        ];

        $commands = $this->mergeCommandsArray($commands, $command);

        return exec(implode(' && ', $commands), $this->outputArray);
    }

    public function cd(string $path = ''): string
    {
        return 'cd '.(!empty($path) ? $this->getPathWithAbsolutePath($path) : $this->getAbsolutePath());
    }

    /**
     * @param array $commands
     * @param mixed $command
     * @return array
     * @throws \Exception
     */
    private function mergeCommandsArray(array $commands, $command)
    {
        if (is_string($command)) {
            $commands[] = $command;
            return $commands;
        } else if (is_array($command)) {
            foreach ($command as $key => $item) {
                if (is_string($key)) {
                    $commands[] = 'echo "'.$key.'"';
                }
                $commands = array_merge($commands, $this->mergeCommandsArray([], $item));
            }
            return $commands;
        }

        throw new \Exception('Command not valid');
    }

    public function getAbsolutePath(): string
    {
        if (!empty($this->absolutePath)) {
            return $this->absolutePath;
        }

        return $this->absolutePath = getcwd();
    }

    public function getPathWithAbsolutePath(string $path): string
    {
        return $this->getAbsolutePath().'/'.trim($path, '/');
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
