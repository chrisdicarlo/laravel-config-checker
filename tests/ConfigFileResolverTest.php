<?php

use ChrisDiCarlo\LaravelConfigChecker\Resolvers\ConfigFileResolver;

it('selects the correct files and directories to check', function () {
    $files = new ConfigFileResolver(realpath(__DIR__.'/fixtures/base'));

    $finder = $files->resolve();

    $filePaths = [];
    foreach ($finder as $file) {
        $filePaths[] = $file->getRealPath();
    }

    expect($finder->count())->toBe(1);
    expect($filePaths)->toContain(realpath(__DIR__.'/fixtures/base/config/app.php'));
    expect($filePaths)->not()->toContain(realpath(__DIR__.'/fixtures/base/resources/views/test-view-one.blade.php'));
    expect($filePaths)->not()->toContain(realpath(__DIR__.'/fixtures/base/resources/views/test-view-two.blade.php'));
    expect($filePaths)->not()->toContain(realpath(__DIR__.'/fixtures/base/app/TestClassOne.php'));
    expect($filePaths)->not()->toContain(realpath(__DIR__.'/fixtures/base/vendor/VendorClassOne.php'));
});

it('selects the correct files and directories when there are nested folders', function () {
    $files = new ConfigFileResolver(realpath(__DIR__.'/fixtures/subfolders'));

    $finder = $files->resolve();

    $filePaths = [];
    foreach ($finder as $file) {
        $filePaths[] = $file->getRealPath();
    }

    expect($finder->count())->toBe(2);
    expect($filePaths)->toContain(realpath(__DIR__.'/fixtures/subfolders/config/app.php'));
    expect($filePaths)->toContain(realpath(__DIR__.'/fixtures/subfolders/config/settings/inside.php'));
    expect($filePaths)->not()->toContain(realpath(__DIR__.'/fixtures/subfolders/app/TestClassOne.php'));
    expect($filePaths)->not()->toContain(realpath(__DIR__.'/fixtures/subfolders/app/TestClassTwo.php'));
    expect($filePaths)->not()->toContain(realpath(__DIR__.'/fixtures/subfolders/vendor/VendorClassOne.php'));
    expect($filePaths)->not()->toContain(realpath(__DIR__.'/fixtures/subfolders/resources/views/test-view-one.blade.php'));
    expect($filePaths)->not()->toContain(realpath(__DIR__.'/fixtures/subfolders/vendor/some-package/TestVendorClass.php'));
});
