<?php

namespace ChrisDiCarlo\LaravelConfigChecker;

use ChrisDiCarlo\LaravelConfigChecker\Commands\LaravelConfigCheckerCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelConfigCheckerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('laravel-config-checker');

        if ($this->app->runningInConsole()) {
            $package->hasCommand(LaravelConfigCheckerCommand::class);
        }
    }
}
