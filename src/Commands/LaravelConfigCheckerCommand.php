<?php

namespace ChrisDiCarlo\LaravelConfigChecker\Commands;

use ChrisDiCarlo\LaravelConfigChecker\Support\BladeFiles;
use ChrisDiCarlo\LaravelConfigChecker\Support\FileChecker;
use ChrisDiCarlo\LaravelConfigChecker\Support\LoadConfigKeys;
use ChrisDiCarlo\LaravelConfigChecker\Support\PhpFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\table;

class LaravelConfigCheckerCommand extends Command
{
    public $signature = 'config:check';

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
        PhpFiles $phpFiles,
        BladeFiles $bladeFiles
    ): int {
        $this->configKeys = $loadConfigKeys();

        $progress = progress(
            label: 'Checking PHP files...',
            steps: $phpFiles(),
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
            steps: $bladeFiles(),
            callback: function ($file, $progress) {
                $progress->hint = "Checking {$file->getRelativePathname()}";

                $content = file_get_contents($file->getRealPath());
                $fileChecker = new FileChecker($this->configKeys, $content);

                foreach ($fileChecker->check() as $issue) {
                    $this->bladeIssues[$file->getRelativePathname()][] = $issue;
                }
            }
        );

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
