# Generate simple linechart SVGs in PHP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ondrej-vrto/linechart.svg?style=flat-square)](https://packagist.org/packages/ondrej-vrto/linechart)
[![Tests](https://img.shields.io/github/actions/workflow/status/ondrej-vrto/linechart/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ondrej-vrto/linechart/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/ondrej-vrto/linechart.svg?style=flat-square)](https://packagist.org/packages/ondrej-vrto/linechart)

This is where your description should go. Try and limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require ondrej-vrto/linechart
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

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Ondrej Vrťo](https://github.com/ondrej-vrto)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
