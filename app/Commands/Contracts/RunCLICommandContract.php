<?php


namespace App\Commands\Contracts;

/**
 * Interface RunCLICommandContract
 * @package App\Commands\Contracts
 */
interface RunCLICommandContract
{
    /**
     * @param string|array $command
     * @return string
     */
    public function cmd($command): string;

    /**
     * @return string
     */
    public function getAbsolutePath(): string;
}
