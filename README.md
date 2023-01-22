# Generate simple php-linechart SVGs in PHP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ondrej-vrto/php-linechart.svg?style=flat-square)](https://packagist.org/packages/ondrej-vrto/php-linechart)
[![Total Downloads](https://img.shields.io/packagist/dt/ondrej-vrto/php-linechart.svg?style=flat-square)](https://packagist.org/packages/ondrej-vrto/php-linechart)

This is where your description should go. Try and limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require ondrej-vrto/php-linechart
```

## Usage

```php
$data = WebVisits::query()
	->select(['day_visit'])
	->whereMorphedTo('visitable', $model)
	->orderByDesc('date')
	->limit(365)
	->get();
// or
$data = [5, 4, 8, 8, 7, 8, 10, 4, 7, 0];

$lineChartSVG = LineChart::new($data)
    ->withStrokeWidth(2)
	->withOrderReversed()
    ->withMaxItemAmount(100)
	->withLockYAxisRange(200)
    ->withDimensions(500, 100)
	->withColorGradient('#4285F4', '#31ACF2', '#2BC9F4')
	->make();
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Ondrej Vrťo](https://github.com/ondrej-vrto)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
