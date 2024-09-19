<?php

namespace ChrisDiCarlo\LaravelConfigChecker\Tests\fixtures\app;

use Illuminate\Support\Facades\Config;

class TestClassOne
{
    public function __construct()
    {
        config('app.valid_key');
        Config::get('app.valid_key');
        Config::has('app.valid_key');
    }
}
