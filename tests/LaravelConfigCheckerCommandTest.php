<?php
use ChrisDiCarlo\LaravelConfigChecker\Commands\LaravelConfigCheckerCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

beforeEach(function () {
    // Reset the configuration and load fixtures
    $this->app->setBasePath(__DIR__ . '/fixtures');
    $this->command = new LaravelConfigCheckerCommand();
    $this->command->setLaravel($this->app);
});

it('handles the command correctly', function () {
    Artisan::call('config:check');
    $output = Artisan::output();

    expect($output)->toContain('No issues found. All config references are valid.');
});

it('outputs config keys correctly', function () {
    $this->command->configKeys = ['app.name', 'database.connections.mysql'];

    ob_start();
    $this->command->outputConfigKeys();
    $output = ob_get_clean();

    expect($output)->toContain('app')->toContain('database');
});

it('flattens config correctly', function () {
    $config = [
        'app' => [
            'name' => 'Laravel',
            'env' => 'local',
        ],
        'database' => [
            'connections' => [
                'mysql' => [
                    'host' => '127.0.0.1',
                ],
            ],
        ],
    ];

    $this->command->flattenConfig($config);

    expect($this->command->configKeys)->toBe([
        'app.name',
        'app.env',
        'database.connections.mysql.host',
    ]);
});

it('loads config keys correctly', function () {
    $this->command->loadConfigKeys();

    expect($this->command->configKeys)->toContain('app.name')->toContain('database.connections.mysql.host');
});

it('displays results correctly', function () {
    $this->command->issues = [
        'file1.php' => [
            ['line' => 10, 'key' => 'app.name', 'type' => 'config()'],
        ],
    ];

    ob_start();
    $this->command->displayResults();
    $output = ob_get_clean();

    expect($output)->toContain('file1.php')->toContain('app.name');
});

it('checks PHP files correctly', function () {
    $this->command->checkPhpFiles();

    expect($this->command->issues)->toBeEmpty();
});

it('checks Blade files correctly', function () {
    $this->command->checkBladeFiles();

    expect($this->command->issues)->toBeEmpty();
});

it('checks for facade usage correctly', function () {
    $file = new SplFileInfo(__DIR__ . '/fixtures/php/invalid.php', '', 'invalid.php');
    $issues = $this->command->checkForFacadeUsage($file);

    expect($issues)->toContain([
        'file' => 'invalid.php',
        'key' => 'app.invalid_key',
        'type' => 'Config::get()',
        'line' => 3,
    ]);
});

it('checks for helper usage correctly', function () {
    $file = new SplFileInfo(__DIR__ . '/fixtures/php/invalid.php', '', 'invalid.php');
    $issues = $this->command->checkForHelperUsage($file);

    expect($issues)->toContain([
        'file' => 'invalid.php',
        'key' => 'app.invalid_key',
        'type' => 'config()',
        'line' => 5,
    ]);
});
