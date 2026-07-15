<?php

declare(strict_types=1);

use ChrisDiCarlo\LaravelConfigChecker\Resolvers\BladeFileResolver;

it('selects the correct files and directories to check', function () {
    $files = new BladeFileResolver(realpath(__DIR__ . '/fixtures/base'));

    $finder = $files->resolve();

    $filePaths = [];
    foreach ($finder as $file) {
        $filePaths[] = $file->getRealPath();
    }

    expect($finder->count())->toBe(2);
    expect($filePaths)->toContain(realpath(__DIR__ . '/fixtures/base/resources/views/test-view-one.blade.php'));
    expect($filePaths)->toContain(realpath(__DIR__ . '/fixtures/base/resources/views/test-view-two.blade.php'));
    expect($filePaths)->not()->toContain(realpath(__DIR__ . '/fixtures/base/app/TestClassOne.php'));
    expect($filePaths)->not()->toContain(realpath(__DIR__ . '/fixtures/base/config/app.php'));
    expect($filePaths)->not()->toContain(realpath(__DIR__ . '/fixtures/base/vendor/VendorClassOne.php'));
});
