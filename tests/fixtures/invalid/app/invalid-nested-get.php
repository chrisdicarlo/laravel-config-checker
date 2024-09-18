<?php

use Illuminate\Support\Facades\Config;

echo Config::get('app.nested.invalid_key');
