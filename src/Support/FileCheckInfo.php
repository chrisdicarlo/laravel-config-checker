<?php

declare(strict_types=1);

namespace ChrisDiCarlo\LaravelConfigChecker\Support;

class FileCheckInfo
{
    public function __construct(
        public readonly int $line,
        public readonly string $key,
        public readonly string $type
    ) {}
}
