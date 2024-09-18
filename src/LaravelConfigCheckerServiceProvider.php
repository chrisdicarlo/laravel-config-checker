<?php

namespace ChrisDiCarlo\LaravelConfigChecker;

use ChrisDiCarlo\LaravelConfigChecker\Commands\LaravelConfigCheckerCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelConfigCheckerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        if ($this->app->runningInConsole()) {
            $package
                ->name('laravel-config-checker')
                ->hasCommand(LaravelConfigCheckerCommand::class);
        }
    }
}
