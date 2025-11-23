<?php

declare(strict_types=1);

namespace ChrisDiCarlo\LaravelConfigChecker;

use ChrisDiCarlo\LaravelConfigChecker\Commands\LaravelConfigCheckerCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelConfigCheckerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('laravel-config-checker');

        $package->hasConfigFile();

        if ($this->app->runningInConsole()) {
            $package->hasCommand(LaravelConfigCheckerCommand::class);
        }
    }
}
