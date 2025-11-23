<?php

declare(strict_types=1);

namespace ChrisDiCarlo\LaravelConfigChecker\Resolvers;

class PhpFileResolver extends AbstractFileResolver
{
    public function excludePaths(): array
    {
        return config('config-checker.php.exclude_paths');
    }

    public function includePaths(): array
    {
        return config('config-checker.php.include_paths');
    }

    public function names(): array
    {
        return config('config-checker.php.names');
    }
}
