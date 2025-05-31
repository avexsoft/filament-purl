<div class="filament-hidden">

![Laravel Created By](https://banners.beyondco.de/Filament%20Purl.png?theme=light&packageManager=composer+require&packageName=avexsoft%2Ffilament-purl&pattern=architect&style=style_2&description=&md=1&showWatermark=0&fontSize=100px&images=switch-horizontal&widths=auto)

</div>

# Filament Persistent Url

[![Latest Version on Packagist](https://img.shields.io/packagist/v/avexsoft/filament-purl.svg?style=flat-square)](https://packagist.org/packages/avexsoft/filament-purl)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/avexsoft/filament-purl/fix-php-code-style-issues.yml?branch=master&label=code%20style&style=flat-square)](https://github.com/avexsoft/filament-purl/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/avexsoft/filament-purl.svg?style=flat-square)](https://packagist.org/packages/avexsoft/filament-purl)

Filament PURL is a Laravel package that seamlessly integrates with Filament to provide a user-friendly interface for managing permalinks (also known as persistent URLs or redirects). Easily create, view, edit, and delete URL redirects directly from your Filament admin panel. Keep your site's links stable and user-friendly, and manage SEO-friendly URLs with ease.

## Installation

You can install the package via composer:

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


## Testing

```bash
composer test
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