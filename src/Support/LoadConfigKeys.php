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

    private function flattenConfig(array $config, string $prefix = '')
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
            $relativePath = $file->getRelativePathname();

            $config = include $file->getRealPath();

            $key = pathinfo($relativePath, PATHINFO_FILENAME);

            $folder = pathinfo($relativePath, PATHINFO_DIRNAME);

            if ($folder !== '.') {
                $key = str_replace('/', '.', $folder).'.'.$key;
            }

            $this->configKeys[] = $key;
            $config = include $file->getRealPath();
            $this->flattenConfig($config, $key);
        }
    }
}
