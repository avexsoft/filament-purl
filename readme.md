<div class="filament-hidden">

![Laravel Created By](https://banners.beyondco.de/Filament%20Purl.png?theme=light&packageManager=composer+require&packageName=avexsoft%2Ffilament-purl&pattern=architect&style=style_2&description=&md=1&showWatermark=0&fontSize=100px&images=switch-horizontal&widths=auto)

</div>

# Filament Persistent Url

[![Latest Version on Packagist](https://img.shields.io/packagist/v/avexsoft/filament-purl.svg?style=flat-square)](https://packagist.org/packages/avexsoft/filament-purl)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/avexsoft/filament-purl/fix-php-code-style-issues.yml?branch=master&label=code%20style&style=flat-square)](https://github.com/avexsoft/filament-purl/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/avexsoft/filament-purl.svg?style=flat-square)](https://packagist.org/packages/avexsoft/filament-purl)

This plugin allows you to define and manage pairs of short, memorable routes (also known as persistent URLs) that will redirect to a provided destination URL. This is useful when you have a bunch of URLs related to your project but are long and difficult to remember.

E.g. You can redirect from https://example.com/r/google to https://www.google.com

## Installation

You can install the package via composer.

```bash
composer require avexsoft/filament-purl
```

## Usage
Add in AdminPanelProvider.php

```php
use Avexsoft\FilamentPurl\FilamentPurlPlugin;

->plugins([
    FilamentPurlPlugin::make(),
])
```


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Avexsoft](https://github.com/avexsoft)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
