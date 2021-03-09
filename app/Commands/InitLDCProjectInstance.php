<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class InitLDCProjectInstance extends RunCLICommand
{
    private string $dir;
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'init:ldc {repo} {--name= : Custom project name} {--e=* : Environment variables for Laravel project} {--ed=* : Environment variables for Docker} {--ed=* : Environment variables for Docker} {--branch=master}';

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
        $repo = $this->argument('repo');
        $name = $this->option('name') ?? $this->getRepoName($repo);
        $dir = (string) Str::of($name)->trim()->slug();
        $this->dir = $dir;
        $this->repo = $repo;

        if (is_dir($this->getPathWithAbsolutePath($dir))) {
            $this->error('Directory: "./'.$dir.'" already created');
            return;
        }

        $this->cloneRepo();
        $this->configureProject();
    }

    private function cloneRepo()
    {
        $this->cmd([
            'git clone '.$this->repo.' '.$this->dir
        ]);
        $this->cmdInProject([
            'git checkout '.$this->option('branch')
        ]);

        $nginxDir = file_exists($this->getPathWithAbsolutePath($this->dir.'/docker/nginx.d')) ? 'nginx.d' : 'nginx/conf.d';
        $cpNginxConf = file_exists($this->getPathWithAbsolutePath($this->dir.'/docker/'.$nginxDir)) ? 'cp example.default.conf default.conf' : 'echo "Nginx conf not found"';

        $this->cmdInProject([
            'cp .env.example .env',
            'cd docker',
            'cp .env.example .env',
            'cd '.$nginxDir,
            $cpNginxConf
        ]);

        $this->setEnvironmentValue(
            $this->getPathWithAbsolutePath($this->dir.'/.env'),
            $this->prepareEnvVars($this->option('e'))
        );

        $values = $this->prepareEnvVars($this->option('ed'));
        $values['COMPOSE_PROJECT_NAME'] = $values['COMPOSE_PROJECT_NAME'] ?? str_replace('-', '_', $this->dir);
        $this->setEnvironmentValue(
            $this->getPathWithAbsolutePath($this->dir.'/docker/.env'),
            $values
        );
        $domain = $values['DOMAINS'] ?? $this->dir.'.localhost';
        $domain = implode(' ', explode(',', trim($domain)));
        $this->replaceSubstringInFile($this->getPathWithAbsolutePath($this->dir.'/docker/'.$nginxDir.'/default.conf'), 'laravel.dev', $domain);
    }

    private function configureProject()
    {
        Artisan::call('run', [
            '--project' => $this->dir
        ]);
        $this->info('Runned');
        $this->cmdInProject([
            'cd docker',
            'docker-compose exec php composer install',
            //'docker-compose exec php php artisan key:generate',
        ]);
        $this->info('configured');
    }

    private function cmdInProject(array $commands)
    {
        $commands = array_merge([
            $this->cd($this->dir)
        ], $commands);
        $this->cmd($commands);
    }

    private function getRepoName(string $repoUrl): string
    {
        $repoExplode = explode('/', $repoUrl);

        return str_replace('.git', '', $repoExplode[count($repoExplode) - 1]);
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
