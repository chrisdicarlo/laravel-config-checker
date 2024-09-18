<?php

use Illuminate\Support\Facades\Config;

echo Config::has('app.nested.invalid_key');
