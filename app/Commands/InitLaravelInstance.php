<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class InitLaravelInstance extends RunCLICommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'init:laravel {name} {--e=* : Environment variables for Laravel project} {--ed=* : Environment variables for Docker}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Initialize a new Laravel app with docker settings';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $dir = (string) Str::of($name)->trim()->slug();

        if (is_dir($this->getPathWithAbsolutePath($dir))) {
            $this->error('Directory: "./'.$dir.'" already created');
            return;
        }

        $this->cmd([
            'git clone https://github.com/laravel/laravel.git '.$dir,
            $this->cd($dir),
            'composer install',
            'cp .env.example .env',
            'php artisan key:generate',
            'git clone https://github.com/acrossoffwest/docker-settings.git docker',
            'cd docker',
            'cp .env.example .env',
            'cd nginx/conf.d',
            'cp example.default.conf default.conf'
        ]);

        $this->setEnvironmentValue(
            $this->getPathWithAbsolutePath($dir.'/.env'),
            $this->prepareEnvVars($this->option('e'))
        );

        $values = $this->prepareEnvVars($this->option('ed'));
        $values['COMPOSE_PROJECT_NAME'] = $values['COMPOSE_PROJECT_NAME'] ?? str_replace('-', '_', $dir);
        $this->setEnvironmentValue(
            $this->getPathWithAbsolutePath($dir.'/docker/.env'),
            $values
        );
        $domain = $values['DOMAINS'] ?? $dir.'.localhost';
        $domain = implode(' ', explode(',', trim($domain)));
        $this->replaceSubstringInFile($this->getPathWithAbsolutePath($dir.'/docker/nginx/conf.d/default.conf'), 'laravel.dev', $domain);
    }

    private function setEnvVars($file, $values)
    {
        if (!file_exists($file)) {
            $this->error('File "'.$file.'" not found');
            return;
        }

        $this->setEnvironmentValue($file, $values);
    }

    private function prepareEnvVars(array $array = [])
    {
        $result = [];

        foreach ($array as $item) {
            $item = explode('=', trim($item));
            if (count($item) < 2) {
                continue;
            }
            $result[$item[0]] = $item[1];
        }

        return $result;
    }

    public function setEnvironmentValue(string $envFile, array $values)
    {
        $str = file_get_contents($envFile);

        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {

                $str = "\n".$str."\n"; // In case the searched variable is in the last line without \n
                $keyPosition = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

                // If key does not exist, add it
                if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
                    $str .= "{$envKey}={$envValue}\n";
                } else {
                    $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
                }
                $str = trim($str, "\n");
            }
        }

        $str = substr($str, 0, -1);
        if (!file_put_contents($envFile, $str)) {
            return false;
        }
        return true;
    }

    public function replaceSubstringInFile(string $file, string $search, string $resplace)
    {
        $str = file_get_contents($file);
        $str = str_replace($search, $resplace, $str);

        if (!file_put_contents($file, $str)) {
            return false;
        }
        return true;
    }
}
