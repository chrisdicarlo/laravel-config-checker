<?php

namespace ChrisDiCarlo\LaravelConfigChecker\Actions;

use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;

class ListFiles
{
    public function __invoke(string $basePath): Finder
    {
        $finder = new Finder;
        return $finder->files()->in($basePath)
            ->name('*.php')
            ->name('*.blade.php')
            ->path('app')
            ->path('database')
            ->path('routes')
            ->path('bootstrap')
            ->path('resources/views')
            ->notPath('vendor');
    }
}
