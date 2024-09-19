<?php

namespace ChrisDiCarlo\LaravelConfigChecker\Support;

use Symfony\Component\Finder\Finder;

class PhpFiles
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
            ->name('*.php')
            ->path('app')
            ->path('database')
            ->path('routes')
            ->path('bootstrap')
            ->notPath('vendor')
            ->notPath('config');

        return $finder;
    }
}
