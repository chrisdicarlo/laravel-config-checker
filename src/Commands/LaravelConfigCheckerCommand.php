<?php

namespace ChrisDiCarlo\LaravelConfigChecker\Commands;

use ChrisDiCarlo\LaravelConfigChecker\Resolvers\BladeFileResolver;
use ChrisDiCarlo\LaravelConfigChecker\Resolvers\PhpFileResolver;
use ChrisDiCarlo\LaravelConfigChecker\Support\FileChecker;
use ChrisDiCarlo\LaravelConfigChecker\Support\LoadConfigKeys;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\table;

class LaravelConfigCheckerCommand extends Command
{
    public $signature = 'config:check {--no-progress : Disable progress bar}';

    public $description = 'Check all references to config values in PHP and Blade files';

    private Collection $configKeys;

    private array $bladeIssues = [];

    private array $phpIssues = [];

    public function getIssues(): Collection
    {
        $combinedIssues = collect([...$this->phpIssues, ...$this->bladeIssues])
            ->filter(fn ($issue) => ! empty($issue));

        return $combinedIssues;
    }

    public function handle(
        LoadConfigKeys $loadConfigKeys,
        PhpFileResolver $phpFiles,
        BladeFileResolver $bladeFiles
    ): int {
        $this->configKeys = $loadConfigKeys();

        if ($this->option('no-progress')) {
            intro('--no-progress option used. Disabling progress bar.');

            info('Checking PHP files...');
            $phpFiles = $phpFiles->resolve();

            foreach ($phpFiles as $file) {
                $content = file_get_contents($file->getRealPath());

                $fileChecker = new FileChecker($this->configKeys, $content);

                foreach ($fileChecker->check() as $issue) {
                    $this->phpIssues[$file->getRelativePathname()][] = $issue;
                }
            }

            info('Checking Blade files...');
            $bladeFiles = $bladeFiles->resolve();
            foreach ($bladeFiles as $file) {
                $content = file_get_contents($file->getRealPath());
                $fileChecker = new FileChecker($this->configKeys, $content);

                foreach ($fileChecker->check() as $issue) {
                    $this->bladeIssues[$file->getRelativePathname()][] = $issue;
                }
            }

        } else {

            $progress = progress(
                label: 'Checking PHP files...',
                steps: $phpFiles->resolve(),
                callback: function ($file, $progress) {
                    $progress->hint = "Checking {$file->getRelativePathname()}";

                    $content = file_get_contents($file->getRealPath());

                    $fileChecker = new FileChecker($this->configKeys, $content);

                    foreach ($fileChecker->check() as $issue) {
                        $this->phpIssues[$file->getRelativePathname()][] = $issue;
                    }
                }
            );

            $progress = progress(
                label: 'Checking Blade files...',
                steps: $bladeFiles->resolve(),
                callback: function ($file, $progress) {
                    $progress->hint = "Checking {$file->getRelativePathname()}";

                    $content = file_get_contents($file->getRealPath());
                    $fileChecker = new FileChecker($this->configKeys, $content);

                    foreach ($fileChecker->check() as $issue) {
                        $this->bladeIssues[$file->getRelativePathname()][] = $issue;
                    }
                }
            );
        }

        if ($this->getIssues()->isEmpty()) {
            info('No issues found. All config references are valid.');

            return self::SUCCESS;
        }

        $this->displayResults();

        return self::FAILURE;
    }

    private function displayResults(): void
    {
        error('Invalid config references found:');

        $rowData = $this->formatIssuesOutput();

        table(
            ['File', 'Line Number', 'Key Referenced', 'Reference Type'],
            $rowData,
        );
    }

    private function formatIssuesOutput(): array
    {
        return $this->getIssues()->sort()
            ->flatMap(function ($issues, $file) {
                return collect($issues)
                    ->sortBy('line')
                    ->map(fn ($issue) => [
                        $file,
                        $issue->line,
                        $issue->key,
                        $issue->type,
                    ]);
            })
            // ->flatten(1)
            ->toArray();
    }
}
