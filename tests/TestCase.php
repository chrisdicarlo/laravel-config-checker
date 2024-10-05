<?php

namespace ChrisDiCarlo\LaravelConfigChecker\Tests;

use ChrisDiCarlo\LaravelConfigChecker\LaravelConfigCheckerServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'ChrisDiCarlo\\LaravelConfigChecker\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-config-checker_table.php.stub';
        $migration->up();
        */
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelConfigCheckerServiceProvider::class,
        ];
    }
}
