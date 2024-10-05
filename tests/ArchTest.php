<?php

declare(strict_types=1);

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('resolvers must implement the FileResolverContract')
    ->expect('ChrisDiCarlo\LaravelConfigChecker\Resolvers')
    ->toImplement('ChrisDiCarlo\LaravelConfigChecker\Contracts\FileResolverContract');
