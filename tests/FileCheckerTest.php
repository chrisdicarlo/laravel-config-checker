<?php

use ChrisDiCarlo\LaravelConfigChecker\Support\FileChecker;
use ChrisDiCarlo\LaravelConfigChecker\Support\FileCheckInfo;
use Illuminate\Support\Collection;

beforeEach(function () {
    $this->configKeys = collect([
        'file',
        'file.valid_key',
        'file.nested',
        'file.nested.key',
    ]);
});
it('returns a collection of FileCheckInfo objects', function () {
    $content = <<<'PHP'
        <?php
            Config::get("invalid_key");
        PHP;

    $fileChecker = new FileChecker($this->configKeys, $content);

    $issues = $fileChecker->check();

    expect($issues)->toBeInstanceOf(Collection::class);
    expect($issues->first())->toBeInstanceOf(FileCheckInfo::class);
});

it('handles content with no facade or helper usage gracefully', function () {
    $content = <<<'PHP'
    <?php
        echo "Hello, World!";
    PHP;

    $fileChecker = new FileChecker($this->configKeys, $content);

    $issues = $fileChecker->check();

    expect($issues)->toBeEmpty();

    $content = <<<'BLADE'
            {{ "Hello, World!" }}
        BLADE;

    $fileChecker = new FileChecker($this->configKeys, $content);

    $issues = $fileChecker->check();

    expect($issues)->toBeEmpty();
});

it('handles content without any issues gracefully', function () {
    $content = <<<'PHP'
        <?php
            Config::get("file.valid_key");
            Config::has("file.valid_key");
            config("file.valid_key");
        PHP;

    $fileChecker = new FileChecker($this->configKeys, $content);

    $issues = $fileChecker->check();

    expect($issues)->toBeEmpty();

    $content = <<<'BLADE'
            {{ Config::get("file.valid_key") }}
            {{ Config::has("file.valid_key") }}
            {{ config("file.valid_key") }}
        BLADE;

    $fileChecker = new FileChecker($this->configKeys, $content);

    $issues = $fileChecker->check();

    expect($issues)->toBeEmpty();
});

it('detects facade and helper usage issues correctly', function () {
    $content = <<<'PHP'
        <?php
            Config::get("file.invalid_key");
            Config::has("file.invalid_key");
            config("file.invalid_key");
            Config::get("file.valid_key");
            Config::has("file.valid_key");
            config("file.valid_key");
        PHP;

    $fileChecker = new FileChecker($this->configKeys, $content);

    $issues = $fileChecker->check();

    expect($issues)->toBeInstanceOf(Collection::class);
    expect($issues->count())->toBe(3);
    expect($issues->contains(function ($issue) {
        return $issue->key === 'file.invalid_key' &&
            $issue->type === 'Config::get()';
    }))->toBeTrue();
    expect($issues->contains(function ($issue) {
        return $issue->key === 'file.invalid_key' &&
            $issue->type === 'Config::has()';
    }))->toBeTrue();
    expect($issues->contains(function ($issue) {
        return $issue->key === 'file.invalid_key' &&
            $issue->type === 'config()';
    }))->toBeTrue();

    $content = <<<'BLADE'
            {{ Config::get("file.invalid_key") }}
            {{ Config::has("file.invalid_key") }}
            {{ config("file.invalid_key") }}
            {{ Config::get("file.valid_key") }}
            {{ Config::has("file.valid_key") }}
            {{ config("file.valid_key") }}
        BLADE;

    $fileChecker = new FileChecker($this->configKeys, $content);

    $issues = $fileChecker->check();

    expect($issues)->toBeInstanceOf(Collection::class);
    expect($issues->count())->toBe(3);
    expect($issues->contains(function ($issue) {
        return $issue->key === 'file.invalid_key' &&
            $issue->type === 'Config::get()';
    }))->toBeTrue();
    expect($issues->contains(function ($issue) {
        return $issue->key === 'file.invalid_key' &&
            $issue->type === 'Config::has()';
    }))->toBeTrue();
    expect($issues->contains(function ($issue) {
        return $issue->key === 'file.invalid_key' &&
            $issue->type === 'config()';
    }))->toBeTrue();
});

it('handles empty content gracefully', function () {
    $content = '';

    $fileChecker = new FileChecker($this->configKeys, $content);

    $issues = $fileChecker->check();

    expect($issues)->toBeEmpty();
});

it('detects issues when there are both valid and invalid references', function () {
    $content = <<<'PHP'
        <?php
            Config::get("file.invalid_key");
            Config::has("file.invalid_key");
            config("file.invalid_key");
            Config::get("file.valid_key");
            Config::has("file.valid_key");
            config("file.valid_key");
        PHP;

    $fileChecker = new FileChecker($this->configKeys, $content);

    $issues = $fileChecker->check();

    expect($issues->contains('key', 'file.valid_key'))->toBeFalse();
    expect($issues->contains('key', 'file.invalid_key'))->toBeTrue();

    $content = <<<'BLADE'
            {{ Config::get("file.invalid_key") }}
            {{ Config::has("file.invalid_key") }}
            {{ config("file.invalid_key") }}
            {{ Config::get("file.valid_key") }}
            {{ Config::has("file.valid_key") }}
            {{ config("file.valid_key") }}
        BLADE;

    $fileChecker = new FileChecker($this->configKeys, $content);

    $issues = $fileChecker->check();

    expect($issues->contains('key', 'file.valid_key'))->toBeFalse();
    expect($issues->contains('key', 'file.invalid_key'))->toBeTrue();
});

it('detects issues for invalid nested keys', function () {
    $content = <<<'PHP'
        <?php
            Config::get("file.nested.invalid_key");
            Config::has("file.nested.invalid_key");
            config("file.nested.invalid_key");
            Config::get("file.valid_key");
            Config::has("file.valid_key");
            config("file.valid_key");
        PHP;

    $fileChecker = new FileChecker($this->configKeys, $content);

    $issues = $fileChecker->check();

    expect($issues->contains('key', 'file.valid_key'))->toBeFalse();
    expect($issues->contains('key', 'file.nested.invalid_key'))->toBeTrue();

    $content = <<<'BLADE'
            {{ Config::get("file.nested.invalid_key") }}
            {{ Config::has("file.nested.invalid_key") }}
            {{ config("file.nested.invalid_key") }}
            {{ Config::get("file.valid_key") }}
            {{ Config::has("file.valid_key") }}
            {{ config("file.valid_key") }}
        BLADE;

    $fileChecker = new FileChecker($this->configKeys, $content);

    $issues = $fileChecker->check();

    expect($issues->contains('key', 'file.valid_key'))->toBeFalse();
    expect($issues->contains('key', 'file.nested.invalid_key'))->toBeTrue();
});

it('ignores methods called config', function () {
    $content = <<<'PHP'
        <?php
            $this->config("some-invalid-key");
            SomeClass::config("another.invalid-key");
            config('some.random.key');
            Config::has('some.invalid.key');
            Config::get('some.other.invalid.key');
        PHP;

    $fileChecker = new FileChecker($this->configKeys, $content);

    $issues = $fileChecker->check();

    expect($issues->count())->toBe(3);
    expect($issues->contains('key', 'some.random.key'))->toBeTrue();
    expect($issues->contains('key', 'some.invalid.key'))->toBeTrue();
    expect($issues->contains('key', 'some.other.invalid.key'))->toBeTrue();
    expect($issues->contains('key', 'some-invalid-key'))->toBeFalse();
    expect($issues->contains('key', 'another.invalid-key'))->toBeFalse();
});
