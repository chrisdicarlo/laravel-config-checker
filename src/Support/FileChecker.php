<?php

namespace ChrisDiCarlo\LaravelConfigChecker\Support;

use Illuminate\Support\Collection;

class FileChecker
{
    public function __construct(
        private readonly Collection $configKeys,
        private readonly string $content,
    ) {}

    public function check(): Collection
    {
        $issues = [];

        $errors = array_merge(
            $this->checkForFacadeUsage($this->content),
            $this->checkForHelperUsage($this->content)
        );

        foreach ($errors as $error) {
            $issues[] = new FileCheckInfo(
                line: $error['line'],
                key: $error['key'],
                type: $error['type'],
            );
        }

        return collect($issues);
    }

    private function checkForFacadeUsage(string $content): array
    {
        $matches = [];

        preg_match_all('/Config::(get|has)\([\'"]([^\'"]+)[\'"]\)/', $content, $matches, PREG_OFFSET_CAPTURE);

        $issues = [];

        foreach ($matches[2] as $index => $match) {
            $key = $match[0];
            $offset = (int) $match[1];
            $lineNumber = substr_count(substr($content, 0, $offset), "\n") + 1;

            if ($this->configKeys->doesntContain($key)) {
                $issues[] = [
                    'key' => $key,
                    'type' => sprintf('Config::%s()', $matches[1][$index][0]),
                    'line' => $lineNumber,
                ];
            }
        }

        return $issues;
    }

    private function checkForHelperUsage(string $content): array
    {
        $matches = [];

        preg_match_all('/[^:>]config\([\'"]([^\'"]+)[\'"]\)/', $content, $matches, PREG_OFFSET_CAPTURE);

        $issues = [];

        foreach ($matches[1] as $match) {
            $key = $match[0];
            $offset = (int) $match[1];
            $lineNumber = substr_count(substr($content, 0, $offset), "\n") + 1;

            if ($this->configKeys->doesntContain($key)) {
                $issues[] = [
                    'key' => $key,
                    'type' => 'config()',
                    'line' => $lineNumber,
                ];
            }
        }

        return $issues;
    }
}
