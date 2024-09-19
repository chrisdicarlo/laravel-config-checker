<?php

namespace ChrisDiCarlo\LaravelConfigChecker\Support;

use Symfony\Component\Finder\Finder;

class BladeFiles
{
    private string $basePath;

    public function __construct(?string $basePath = null)
    {
        if (! $basePath) {
            $basePath = base_path();
        }

        $this->basePath = $basePath;
    }

    public function __invoke(): Finder
    {
        $finder = new Finder;
        $finder->files()->in($this->basePath)
            ->name('*.blade.php')
            ->path('resources/views')
            ->notPath('vendor');

        return $finder;
    }
}
