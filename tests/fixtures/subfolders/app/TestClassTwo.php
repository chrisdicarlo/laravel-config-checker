<?php

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
