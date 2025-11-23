<?php

declare(strict_types=1);

namespace ChrisDiCarlo\LaravelConfigChecker\Resolvers;

class BladeFileResolver extends AbstractFileResolver
{
    public function excludePaths(): array
    {
        return config('config-checker.blade.exclude_paths');
    }

    public function includePaths(): array
    {
        return config('config-checker.blade.include_paths');
    }

    public function names(): array
    {
        return config('config-checker.blade.names');
    }
}
