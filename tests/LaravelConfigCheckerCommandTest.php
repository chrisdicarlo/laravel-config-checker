<?php

use function Pest\Laravel\artisan;

it('displays a message when there are no issues', function () {
    // set the base path for the application
    $this->app->setBasePath(realpath(__DIR__.'/fixtures/valid'));

    artisan('config:check')
        ->expectsOutputToContain('No issues found. All config references are valid.')
        ->assertExitCode(0);
});

it('displays a message when there are issues', function () {
    // set the base path for the application
    $this->app->setBasePath(realpath(__DIR__.'/fixtures/invalid'));

    artisan('config:check')
        ->expectsOutputToContain('Invalid config references found:')
        ->expectsOutputToContain('app'.DIRECTORY_SEPARATOR.'TestClassOne.php')
        ->expectsOutputToContain('app'.DIRECTORY_SEPARATOR.'TestClassTwo.php')
        ->expectsOutputToContain('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'test-view-one.blade.php')
        ->expectsOutputToContain('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'test-view-two.blade.php')
        ->assertExitCode(1);
});
