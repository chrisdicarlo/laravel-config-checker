<?php

namespace ChrisDiCarlo\LaravelConfigChecker\Support;

use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;

class LoadConfigKeys
{
    private array $configKeys = [];

    private string $configPath;

    public function __construct(?string $configPath = null)
    {
        if (! $configPath) {
            $configPath = base_path('config');
        }

        $this->configPath = $configPath;
    }

    public function __invoke(): array|Collection
    {
        $this->loadConfigKeys();

        // return $this->configKeys;
        return collect($this->configKeys);
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
        $configPath = $this->configPath;
        $finder = new Finder;
        $finder->files()->in($configPath)->name('*.php');

        foreach ($finder as $file) {
            $this->configKeys[] = basename($file->getFilename(), '.php');

            $config = include $file->getRealPath();

            $this->flattenConfig($config, basename($file->getFilename(), '.php'));
        }
    }
}
