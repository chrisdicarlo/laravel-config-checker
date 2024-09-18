<?php

namespace ChrisDiCarlo\LaravelConfigChecker\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

class LaravelConfigCheckerCommand extends Command
{
    public $signature = 'config:check';

    public $description = 'Check all references to config values in PHP and Blade files';

    private $configKeys = [];

    private array $issues = [];

    public function handle(): int
    {
        $this->loadConfigKeys();
        $this->checkPhpFiles();
        $this->checkBladeFiles();

        $this->displayResults();

        return self::SUCCESS;
    }

    private function flattenConfig($config, $prefix = '')
    {
        foreach ($config as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_array($value)) {
                $this->flattenConfig($value, $fullKey);
            } else {
                $this->configKeys[] = $fullKey;
            }
        }
    }

    private function loadConfigKeys()
    {
        $configPath = $this->laravel->configPath();
        $finder = new Finder;
        $finder->files()->in($configPath)->name('*.php');

        foreach ($finder as $file) {
            $config = include $file->getRealPath();
            $this->flattenConfig($config, basename($file->getFilename(), '.php'));
        }
    }

    private function displayResults(): void
    {
        $issues = collect($this->issues)->filter(fn ($issue) => ! empty($issue));

        if ($issues->isEmpty()) {
            $this->info('No issues found. All config references are valid.');

            return;
        }

        $this->error('Found '.$issues->count().' issue(s) with config references:');
        $issues->each(function ($issues, $file) {
            $this->line("File: $file");
            collect($issues)->each(function ($issue) {
                $this->line("Key: {$issue['key']}");
                $this->line("Type: {$issue['type']}");
                $this->line('---');
            });
        });
    }

    private function checkPhpFiles(): void
    {
        $finder = new Finder;
        $finder->files()->in($this->laravel->basePath())
            ->name('*.php')
            ->notPath('vendor');
    }

    private function checkBladeFiles(): void
    {
        $finder = new Finder;
        $finder->files()->in($this->laravel->basePath())
            ->name('*.blade.php')
            ->notPath('vendor');
    }

    private function checkForFacadeUsage($file): array
    {
        $content = file_get_contents($file->getRealPath());
        $matches = [];

        preg_match_all('/Config::(?:get|has)\([\'"]([^\'"]+)[\'"]\)/', $content, $matches);

        $issues = [];

        foreach ($matches[1] as $match) {
            if (! in_array($match, $this->configKeys)) {
                $issues[] = [
                    'file' => $file->getRelativePathname(),
                    'key' => $match,
                    'type' => 'facade',
                ];
            }
        }

        return $issues;
    }

    private function checkForHelperUsage($file): array
    {
        $content = file_get_contents($file->getRealPath());
        $matches = [];

        preg_match_all('/config\([\'"]([^\'"]+)[\'"]\)/', $content, $matches);

        $issues = [];

        foreach ($matches[1] as $match) {
            if (! in_array($match, $this->configKeys)) {
                $issues[] = [
                    'file' => $file->getRelativePathname(),
                    'key' => $match,
                    'type' => 'helper',
                ];
            }
        }

        return $issues;
    }

    private function checkFiles(Finder $finder): void
    {
        foreach ($finder as $file) {
            $this->issues[$file->getRelativePath()] = array_merge(
                $this->issues[$file->getRelativePath()] ?? [],
                $this->checkForFacadeUsage($file),
                $this->checkForHelperUsage($file)
            );
        }

        // if (empty($issues)) {
        //     $this->info('No issues found. All config references are valid.');
        // } else {
        //     $this->error('Found ' . count($issues) . ' issue(s) with config references:');
        //     foreach ($issues as $issue) {
        //         $this->line("File: {$issue['file']}");
        //         $this->line("Key: {$issue['key']}");
        //         $this->line("Type: {$issue['type']}");
        //         $this->line('---');
        //     }
        // }
    }
}
