<?php

use ChrisDiCarlo\LaravelConfigChecker\Support\LoadConfigKeys;
use Illuminate\Support\Collection;

beforeEach(function () {
    $this->configKeys = collect([
        'app',
        'app.valid_key',
        'app.nested',
        'app.nested.key',
    ]);

    $this->nestedFolderConfigKeys = collect([
        'app',
        'app.valid_key',
        'app.nested',
        'app.nested.key',
        'settings.inside',
        'settings.inside.inside_subfolder_valid_key',
        'settings.inside.inside_subfolder_nested',
        'settings.inside.inside_subfolder_nested.inside_subfolder_nested_key',
    ]);
});

it('loads config keys from the config directory', function () {
    $loadConfigKeys = new LoadConfigKeys(realpath(__DIR__ . '/fixtures/base/config'));

    $configKeys = $loadConfigKeys();

    expect($configKeys)->toMatchArray($this->configKeys);
});

it('loads config keys from the default config directory', function () {
    $loadConfigKeys = new LoadConfigKeys;

    $configKeys = $loadConfigKeys();

    expect($configKeys)->contains('app.nested.key')->toBeFalse();
    expect($configKeys)->contains('app.env')->toBeTrue();
    expect($configKeys->count())->toBeGreaterThan(2);
});

it('loads config keys from the config directory with nested subfolders', function () {
    $loadConfigKeys = new LoadConfigKeys(realpath(__DIR__ . '/fixtures/subfolders/config'));

    $configKeys = $loadConfigKeys();

    foreach ($this->nestedFolderConfigKeys as $configKey) {
        expect($configKeys)->toContain($configKey);
    }
});

it('returns a collection of config keys', function () {
    $loadConfigKeys = new LoadConfigKeys(realpath(__DIR__ . '/fixtures/base/config'));

    $configKeys = $loadConfigKeys();

    expect($configKeys)->toBeInstanceOf(Collection::class);
});

it('converts an array of config keys to dotted notation', function () {
    $loadConfigKeys = new LoadConfigKeys(realpath(__DIR__ . '/fixtures/base/config'));

    $configKeys = [
        'app',
        'app.valid_key',
        'app.nested',
        'app.nested.key',
    ];

    expect($loadConfigKeys())->toMatchArray($configKeys);
});
