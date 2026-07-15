<?php

declare(strict_types=1);

namespace ChrisDiCarlo\LaravelConfigChecker\Tests\fixtures\app;

use Illuminate\Support\Facades\Config;

class TestClassTwo
{
    public function __construct()
    {
        config('app.invalid_key');
        Config::get('app.invalid_key');
        Config::has('app.invalid_key');
    }
}
