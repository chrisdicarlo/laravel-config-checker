<?php

namespace ChrisDiCarlo\LaravelConfigChecker\Support;

class FileCheckInfo
{
    public function __construct(
        public readonly int $line,
        public readonly string $key,
        public readonly string $type
    ) {}
}
