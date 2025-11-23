<?php

declare(strict_types=1);

namespace ChrisDiCarlo\LaravelConfigChecker\Resolvers;

class ConfigFileResolver extends AbstractFileResolver
{
    public function excludePaths(): array
    {
        return config('config-checker.config.exclude_paths');
    }

    public function includePaths(): array
    {
        return config('config-checker.config.include_paths');
    }

    public function names(): array
    {
        return config('config-checker.config.names');
    }
}
