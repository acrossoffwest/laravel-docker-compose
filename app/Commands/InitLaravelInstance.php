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
    protected $signature = 'init:laravel {name} {--e=* : Environment variables for Laravel project} {--ed=* : Environment variables for Docker} {--dir= : Custom directory to clone repository} {--repo= : Repository URL} {--fork}';

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
        $repoUrl = !empty($this->option('repo')) ? $this->option('repo') : 'https://github.com/laravel/laravel.git';

        $name = $this->argument('name');
        $dir = rtrim($this->option('dir'), '/') ?? (string) Str::of($name)->trim()->slug();
        $domain = $dir.'.localhost';

        if (is_dir($this->getPathWithAbsolutePath($dir))) {
            $this->error('Directory: "./'.$dir.'" already created');
            return;
        }

        $this->cmd([
            'git clone '.$repoUrl.' '.$dir,
            $this->cd($dir),
            $this->option('fork') ? 'rm -rf .git' : ' ',
            'composer install',
            'cp .env.example .env',
            'php artisan key:generate',
            $this->dockerPathAlreadyCreated($dir) ? '' : 'git clone https://github.com/acrossoffwest/docker-settings.git docker',
            'cd docker',
            'rm -rf .git',
            'cp .env.example .env',
            'cd nginx/conf.d',
            'cp example.default.conf default.conf'
        ]);

        $this->setEnvironmentValue(
            $this->getPathWithAbsolutePath($dir.'/.env'),
            $this->prepareEnvVars($this->option('e'), [
                'APP_URL' => 'http://'.$domain
            ])
        );

        $values = $this->prepareEnvVars($this->option('ed'), [], 'docker');
        $values['DOMAINS'] = isset($values['DOMAINS']) ? $values['DOMAINS'].','.$domain : $domain;
        $values['PMA_DOMAIN'] = $values['PMA_DOMAIN'] ?? 'pma.'.$domain;
        $values['COMPOSE_PROJECT_NAME'] = $values['COMPOSE_PROJECT_NAME'] ?? str_replace('-', '_', $dir);

        $this->setEnvironmentValue(
            $this->getPathWithAbsolutePath($dir.'/docker/.env'),
            $values
        );

        $this->replaceSubstringInFile(
            $this->getPathWithAbsolutePath($dir.'/docker/nginx/conf.d/default.conf'),
            'laravel.dev',
            $this->prepareDomainForNginx($values['DOMAINS'])
        );

        $this->info(
            'That\'s all. You can open your project by url: '."\n".
            'http://'.$domain."\n".
            'Note: You have to change storage folder permissions by this command: '."\n".
            'sudo chmod -R a+rws ./'.$dir.'/storage/'
        );
    }

    private function dockerPathAlreadyCreated($dir)
    {
        return is_dir($this->getPathWithAbsolutePath($dir.'/docker'));
    }

    private function setEnvVars($file, $values)
    {
        if (!file_exists($file)) {
            $this->error('File "'.$file.'" not found');
            return;
        }

        $this->setEnvironmentValue($file, $values);
    }

    private function prepareEnvVars(array $array = [], array $vars = [], string $default = 'laravel')
    {
        $result = [];

        foreach ($array as $item) {
            $item = explode('=', trim($item));
            if (count($item) < 2) {
                continue;
            }
            $result[$item[0]] = $item[1];
        }

        return array_merge(config('default-env-variables.'.$default), $vars, $result);
    }

    private function prepareDomainForNginx(string $domain)
    {
        return implode(' ', explode(',', trim($domain)));
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
