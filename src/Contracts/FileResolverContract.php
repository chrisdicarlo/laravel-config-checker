<?php

namespace ChrisDiCarlo\LaravelConfigChecker\Contracts;

interface FileResolverContract
{
    public function excludePaths(): array;

    public function includePaths(): array;

    public function names(): array;

    public function resolve(): iterable;
}
