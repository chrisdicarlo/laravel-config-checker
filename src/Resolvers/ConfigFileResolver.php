<?php

declare(strict_types=1);

namespace ChrisDiCarlo\LaravelConfigChecker\Resolvers;

class ConfigFileResolver extends AbstractFileResolver
{
    public function excludePaths(): array
    {
        return ['vendor'];
    }

    public function includePaths(): array
    {
        return ['config'];
    }

    public function names(): array
    {
        return ['*.php'];
    }
}
