<?php

namespace ChrisDiCarlo\LaravelConfigChecker\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\table;

class LaravelConfigCheckerCommand extends Command
{
    public $signature = 'config:check';

    public $description = 'Check all references to config values in PHP and Blade files';

    private $configKeys = [];

    private array $issues = [];

    public function handle(): int
    {
        $this->loadConfigKeys();

        if ($this->output->isVerbose()) {
            $this->outputConfigKeys();
        }

        $this->checkPhpFiles();
        $this->checkBladeFiles();

        $this->displayResults();

        return self::SUCCESS;
    }

    private function outputConfigKeys(): void
    {
        table(
            ['File', '# of Keys'],
            collect($this->configKeys)->groupBy(fn ($key) => explode('.', $key)[0])
                ->map(fn ($subkeys, $key) => ['key' => $key, 'count' => $subkeys->count()])
                ->sort()
                ->values()
                ->toArray()
        );
    }

    private function flattenConfig($config, $prefix = '')
    {
        foreach ($config as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : $key;

            $this->configKeys[] = $fullKey;

            if (is_array($value)) {
                $this->flattenConfig($value, $fullKey);
            }
        }
    }

    private function loadConfigKeys()
    {
        $configPath = $this->laravel->configPath();
        $finder = new Finder;
        $finder->files()->in($configPath)->name('*.php');

        foreach ($finder as $file) {
            $this->configKeys[] = basename($file->getFilename(), '.php');

            $config = include $file->getRealPath();

            $this->flattenConfig($config, basename($file->getFilename(), '.php'));
        }
    }

    private function displayResults(): void
    {
        $issues = collect($this->issues)->filter(fn ($issue) => ! empty($issue));

        if ($issues->isEmpty()) {
            info('No issues found. All config references are valid.');

            return;
        }

        error('Issues found! Invalid config references detected:');

        table(
            ['File', 'Line Number', 'Key', 'Reference Type'],
            $issues->sort()->map(function ($issues, $file) {
                return collect($issues)->map(function ($issue) use ($file) {
                    return [
                        'file' => $file,
                        'line' => $issue['line'],
                        'key' => $issue['key'],
                        'type' => $issue['type'],
                    ];
                });
            })->flatten(1)->toArray()
        );
    }

    private function checkPhpFiles(): void
    {
        $finder = new Finder;
        $finder->files()->in($this->laravel->basePath())
            ->name('*.php')
            ->path('app')
            ->path('database')
            ->path('routes')
            ->path('bootstrap')
            ->notPath('vendor');

        $progress = progress(
            label: 'Checking PHP files...',
            steps: $finder,
            callback: function ($file, $progress) {
                $progress->hint = "Checking {$file->getRelativePathname()}";

                $this->issues[$file->getRelativePathname()] = array_merge(
                    $this->issues[$file->getRelativePathname()] ?? [],
                    $this->checkForFacadeUsage($file),
                    $this->checkForHelperUsage($file)
                );
            }
        );
    }

    private function checkBladeFiles(): void
    {
        $finder = new Finder;
        $finder->files()->in($this->laravel->basePath())
            ->name('*.blade.php')
            ->notPath('vendor');

        $progress = progress(
            label: 'Checking Blade files...',
            steps: $finder,
            callback: function ($file, $progress) {
                $progress->hint = "Checking {$file->getRelativePathname()}";

                $this->issues[$file->getRelativePathname()] = array_merge(
                    $this->issues[$file->getRelativePathname()] ?? [],
                    $this->checkForFacadeUsage($file),
                    $this->checkForHelperUsage($file)
                );
            }
        );
    }

    private function checkForFacadeUsage($file): array
    {
        $content = file_get_contents($file->getRealPath());
        $matches = [];

        preg_match_all('/Config::(get|has)\([\'"]([^\'"]+)[\'"]\)/', $content, $matches, PREG_OFFSET_CAPTURE);

        $issues = [];

        foreach ($matches[2] as $index => $match) {
            $key = $match[0];
            $offset = (int) $match[1];
            $lineNumber = substr_count(substr($content, 0, $offset), "\n") + 1;

            if (! in_array($key, $this->configKeys)) {
                $issues[] = [
                    'file' => $file->getRelativePathname(),
                    'key' => $key,
                    'type' => sprintf('Config::%s()', $matches[1][$index][0]),
                    'line' => $lineNumber,
                ];
            }
        }

        return $issues;
    }

    private function checkForHelperUsage($file): array
    {
        $content = file_get_contents($file->getRealPath());
        $matches = [];

        preg_match_all('/config\([\'"]([^\'"]+)[\'"]\)/', $content, $matches, PREG_OFFSET_CAPTURE);

        $issues = [];

        foreach ($matches[1] as $match) {
            $key = $match[0];
            $offset = (int) $match[1];
            $lineNumber = substr_count(substr($content, 0, $offset), "\n") + 1;

            if (! in_array($key, $this->configKeys)) {
                $issues[] = [
                    'file' => $file->getRelativePathname(),
                    'key' => $key,
                    'type' => 'config()',
                    'line' => $lineNumber,
                ];
            }
        }

        return $issues;
    }
}
