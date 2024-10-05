<?php

namespace ChrisDiCarlo\LaravelConfigChecker\Resolvers;

class PhpFileResolver extends AbstractFileResolver
{
    public function excludePaths(): array
    {
        return ['vendor', 'config'];
    }

    public function includePaths(): array
    {
        return ['app', 'database', 'routes', 'bootstrap'];
    }

    public function names(): array
    {
        return ['*.php'];
    }
}
