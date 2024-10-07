<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;

use function Pest\Laravel\artisan;

it('displays a message when there are no issues', function () {
    $this->app->setBasePath(realpath(__DIR__ . '/fixtures/valid'));
    Config::set('app.valid_key', 'Laravel Config Checker');
    Config::set('app.nested', [
        'key' => 'value',
    ]);

    artisan('config:check')
        ->expectsOutputToContain('No issues found. All config references are valid.')
        ->assertExitCode(0);
});

it('displays a message when there are issues', function () {
    $this->app->setBasePath(realpath(__DIR__ . '/fixtures/invalid'));

    Config::set('app.valid_key', 'Laravel Config Checker');
    Config::set('app.nested', [
        'key' => 'value',
    ]);

    artisan('config:check')
        ->expectsOutputToContain('Invalid config references found:')
        ->expectsOutputToContain('app' . DIRECTORY_SEPARATOR . 'TestClassOne.php')
        ->expectsOutputToContain('app' . DIRECTORY_SEPARATOR . 'TestClassTwo.php')
        ->expectsOutputToContain('resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'test-view-one.blade.php')
        ->expectsOutputToContain('resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'test-view-two.blade.php')
        ->assertExitCode(1);
});

it('disables the progress bar when the --no-progress option is used', function () {
    $this->app->setBasePath(realpath(__DIR__ . '/fixtures/valid'));

    Config::set('app.valid_key', 'Laravel Config Checker');
    Config::set('app.nested', [
        'key' => 'value',
    ]);

    artisan('config:check', ['--no-progress' => true])
        ->expectsOutputToContain('--no-progress option used. Disabling progress bar.')
        ->expectsOutputToContain('Checking PHP files...')
        ->expectsOutputToContain('Checking Blade files...')
        ->expectsOutputToContain('No issues found. All config references are valid.')
        ->assertExitCode(0);
});

it('skips checking blade files when the --no-blade option is used', function () {
    $this->app->setBasePath(realpath(__DIR__ . '/fixtures/valid'));

    Config::set('app.valid_key', 'Laravel Config Checker');
    Config::set('app.nested', [
        'key' => 'value',
    ]);

    artisan('config:check', ['--no-blade' => true])
        ->expectsOutputToContain('Checking PHP files...')
        ->doesntExpectOutputToContain('Checking Blade files...')
        ->expectsOutputToContain('No issues found. All config references are valid.')
        ->assertExitCode(0);

    artisan('config:check', ['--no-blade' => true, '--no-progress' => true])
        ->expectsOutputToContain('Checking PHP files...')
        ->doesntExpectOutputToContain('Checking Blade files...')
        ->expectsOutputToContain('No issues found. All config references are valid.')
        ->assertExitCode(0);
});

it('skips checking php files when the --no-php option is used', function () {
    $this->app->setBasePath(realpath(__DIR__ . '/fixtures/valid'));

    Config::set('app.valid_key', 'Laravel Config Checker');
    Config::set('app.nested', [
        'key' => 'value',
    ]);

    artisan('config:check', ['--no-php' => true])
        ->expectsOutputToContain('Checking Blade files...')
        ->doesntExpectOutputToContain('Checking PHP files...')
        ->expectsOutputToContain('No issues found. All config references are valid.')
        ->assertExitCode(0);

    artisan('config:check', ['--no-php' => true, '--no-progress' => true])
        ->expectsOutputToContain('Checking Blade files...')
        ->doesntExpectOutputToContain('Checking PHP files...')
        ->expectsOutputToContain('No issues found. All config references are valid.')
        ->assertExitCode(0);
});
