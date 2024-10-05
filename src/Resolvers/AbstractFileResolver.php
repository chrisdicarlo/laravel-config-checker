<?php

namespace ChrisDiCarlo\LaravelConfigChecker\Resolvers;

use ChrisDiCarlo\LaravelConfigChecker\Contracts\FileResolverContract;
use Symfony\Component\Finder\Finder;

abstract class AbstractFileResolver implements FileResolverContract
{
    private string $basePath;

    private ?Finder $finder = null;

    public function __construct(?string $basePath = null)
    {
        if (! $basePath) {
            $basePath = base_path();
        }

        $this->basePath = $basePath;
    }

    abstract public function excludePaths(): array;

    abstract public function includePaths(): array;

    abstract public function names(): array;

    public function resolve(): iterable
    {
        if (! $this->finder) {
            $this->finder = $this->configureFinder();
        }

        return $this->finder;
    }

    private function configureFinder(): Finder
    {
        $finder = Finder::create()
            ->files()
            ->in($this->basePath);

        foreach ($this->excludePaths() as $excludePath) {
            $finder->notPath($excludePath);
        }

        foreach ($this->includePaths() as $includePath) {
            $finder->path($includePath);
        }

        foreach ($this->names() as $name) {
            $finder->name($name);
        }

        return $finder;
    }
}
