<?php

namespace ChrisDiCarlo\LaravelConfigChecker\Resolvers;

class BladeFileResolver extends AbstractFileResolver
{
    public function excludePaths(): array
    {
        return ['vendor'];
    }

    public function includePaths(): array
    {
        return ['resources/views'];
    }

    public function names(): array
    {
        return ['*.blade.php'];
    }
}
