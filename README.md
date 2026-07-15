# Check for undefined configuration key references in your Laravel project.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/chrisdicarlo/laravel-config-checker.svg?style=flat-square)](https://packagist.org/packages/chrisdicarlo/laravel-config-checker)
[![GitHub Tests (8.2 / 8.3) Action Status](https://img.shields.io/github/actions/workflow/status/chrisdicarlo/laravel-config-checker/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/chrisdicarlo/laravel-config-checker/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Tests (8.1) Action Status](https://img.shields.io/github/actions/workflow/status/chrisdicarlo/laravel-config-checker/run-php-8.1-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/chrisdicarlo/laravel-config-checker/actions?query=workflow%3Arun-php-8.1-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/chrisdicarlo/laravel-config-checker/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/chrisdicarlo/laravel-config-checker/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/chrisdicarlo/laravel-config-checker.svg?style=flat-square)](https://packagist.org/packages/chrisdicarlo/laravel-config-checker)

This package adds an Artisan command to check for invalid configuration file references in your application code and Blade views.

## Support me

**Feel like shouting out a thank you?** [Buy me a coffee! ☕️](https://buymeacoffee.com/chrisdicarlo)

## Installation

You can install the package via composer:

```bash
composer require chrisdicarlo/laravel-config-checker
```

After installing the package, you can publish the configuration file:

```bash
php artisan vendor:publish --tag="config-checker-config"
```

## Usage
From the command-line, simply run:
```bash
php artisan config:check
```

The command will scan your Php code under `app`, `database`, `routes`, `bootstrap` and your Blade views under `resources/views`.  
Any errors will be displayed in a table with information on the location and missing reference:

![Sample Output](output-sample.png)

### Customizing paths

If you want the command to scan PHP/Blade code under custom paths, you can add or update entries in
`config/config-checker.php`.

### Disabling progress

To disable progress bars (e.g. in CI), pass the `--no-progress` flag when running the command:
```bash
php artisan config:check --no-progress
```

### Specifying the file type to check

To skip checking of Php or Blade files, pass `--no-php` or `--no-blade` respectively:
```bash
php artisan config:check --no-php
php artisan config:check --no-blade
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Chris Di Carlo](https://github.com/chrisdicarlo)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
