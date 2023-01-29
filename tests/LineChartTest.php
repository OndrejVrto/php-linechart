<?php

declare(strict_types=1);

use OndrejVrto\LineChart\LineChart;

it('create svg from static method', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])->make();

    expect($svg)->toBeString()->and($svg)->not()->toBeEmpty();
});

test('stringable interface', function (): void {
    $svg = (string) new LineChart([0, 1, 2, 3, 4]);

    expect($svg)->toBeString()->and($svg)->not()->toBeEmpty();
});

test('input data conversion', function (mixed $input, string $result): void {
    $svgArray = LineChart::new($input)->make();
    $svgVariadic = LineChart::new(...$input)->make();

    expect($svgArray)->toContain('points="'.$result.'"');
    expect($svgVariadic)->toContain('points="'.$result.'"');
})->with([
    'integer values greater than zero' => [
        [0, 1, 2, 3, 4],
        '0 0 1 1 2 2 3 3 4 4'
    ],
    'numbers in string' => [
        ["0", "01", "002", "3", "4.05"],
        '0 0.00 1 1.00 2 2.00 3 3.00 4 4.05'
    ],
    'float' => [
        [5.5, 8.1],
        '0 5.50 1 8.10'
    ],
    'long float' => [
        [5.5555555, 100.1111111],
        '0 5.56 1 100.11'
    ],
    'booleans' => [
        [true, false, true, true],
        '0 1 1 0 2 1 3 1'
    ],
    'negative number' => [
        [-1, 0, 1],
        '0 0 1 1 2 2'
    ],
    'nested arrays' => [
        [0, 1, 2, "foo" => [3, 4, "bar" => [5, 6]]],
        '0 0 1 1 2 2 3 3 4 4 5 5 6 6'
    ],
    'one value' => [
        [5],
        '0 0 1 5'
    ],
    'empty array' => [
        [],
        '0 0 1 0'
    ],
    'null' => [
        [null],
        '0 0 1 0'
    ],
    'bad values' => [
        ['foo', 'bar'],
        '0 0 1 0'
    ],
    'conbinations' => [
        [-5, 0, true, false, 'foo', [5.55555, 1000]],
        '0 0 1 5 2 6 3 5 4 10.56 5 1005'
    ],
    'collection' => [
        collect([1, 2, 3]),
        '0 1 1 2 2 3'
    ],
]);

it('default four colors', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])->make();

    expect($svg)->toContain('<stop stop-color="#fbd808" offset="0"></stop>');
    expect($svg)->toContain('<stop stop-color="#ff9005" offset="0.34"></stop>');
    expect($svg)->toContain('<stop stop-color="#f9530b" offset="0.67"></stop>');
    expect($svg)->toContain('<stop stop-color="#ff0000" offset="1"></stop>');
});

it('default four colors if is set null', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])->withColorGradient(null)->make();

    expect($svg)->toContain('<stop stop-color="#fbd808" offset="0"></stop>');
    expect($svg)->toContain('<stop stop-color="#ff9005" offset="0.34"></stop>');
    expect($svg)->toContain('<stop stop-color="#f9530b" offset="0.67"></stop>');
    expect($svg)->toContain('<stop stop-color="#ff0000" offset="1"></stop>');
});

test('three color in default browser text format', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])
        ->withColorGradient('Green', 'Orange', 'Red')
        ->make();

    expect($svg)->toContain('stop-color="#008000" offset="0"');
    expect($svg)->toContain('stop-color="#ffa500" offset="0.5"');
    expect($svg)->toContain('stop-color="#ff0000" offset="1"');
});

it('color in format', function (string $color): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])
        ->withColorGradient($color)
        ->make();

    expect($svg)->toContain('stop-color="#0000ff"');
})->with([
    'text' => 'Blue',
    'hex'  => '#0000ff',
    'rgb'  => 'rgb(0, 0, 255)',
    'rgba' => 'rgba(0, 0, 255, 1.0)',
    'hsl'  => 'hsl(240, 100%, 50%)',
    'hsla' => 'hsla(240, 100%, 50%, 1.0)',
    'cmyk' => 'cmyk(100%,100%,0%,0%)',
    'xyz'  => 'xyz(18.05,7.22,95.05)',
    // 'hsb'    => 'hsb(241, 100%, 50%)',        // also possible, but the conversion is slightly imprecise
    // 'CIELab' => 'CIELab(32.3,79.2,-107.86)',  // also possible, but the conversion is slightly imprecise
]);

test('setting the default value if a non-existent color format is required', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])
        ->withColorGradient('foo', 'bar')
        ->make();

    expect($svg)->toContain('<stop stop-color="#fbd808" offset="0"></stop>');
    expect($svg)->toContain('<stop stop-color="#ff9005" offset="0.34"></stop>');
    expect($svg)->toContain('<stop stop-color="#f9530b" offset="0.67"></stop>');
    expect($svg)->toContain('<stop stop-color="#ff0000" offset="1"></stop>');
});

it('default size', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])->make();

    expect($svg)->toContain('viewBox="0 0 200 30"');
    expect($svg)->toContain('width="200"');
    expect($svg)->toContain('height="30"');
});

test('change sizes', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])
        ->withDimensions(1000, 100)
        ->make();

    expect($svg)->toContain('viewBox="0 0 1000 100"');
    expect($svg)->toContain('width="1000"');
    expect($svg)->toContain('height="100"');
});

test('change only the width using a negative float number', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])
        ->withDimensions(-333.1234)
        ->make();

    expect($svg)->toContain('viewBox="0 0 333 30"');
    expect($svg)->toContain('width="333"');
    expect($svg)->toContain('height="30"');
});

it('default stroke', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])->make();

    expect($svg)->toContain('stroke-width="2"');
});

test('set stroke to long float value', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])
        ->withStrokeWidth(10.5555555555)
        ->make();

    expect($svg)->toContain('stroke-width="10.56"');
});

test('set stroke to negative value', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])
        ->withStrokeWidth(-5)
        ->make();

    expect($svg)->toContain('stroke-width="5"');
});

test('lock Y axis', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])
        ->withStrokeWidth(20)  // set stroke to 20 for preety scale number
        ->withLockYAxisRange(10)
        ->make();

    expect($svg)->toContain('transform="scale(45 -1) translate(0.2222 -20)"');
});

test('cut down on the number of items', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])
        ->withMaxItemAmount(3)
        ->make();

    expect($svg)->toContain('points="0 0 1 1 2 2"');
});

test('trim the list so that at least two points remain with 2', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])
        ->withMaxItemAmount(2)
        ->make();

    expect($svg)->toContain('points="0 0 1 1"');
});

test('trim the list so that at least two points remain with 1', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])
        ->withMaxItemAmount(1)
        ->make();

    expect($svg)->toContain('points="0 0 1 1"');
});

test('trim the list so that at least two points remain with 0', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])
        ->withMaxItemAmount(0)
        ->make();

    expect($svg)->toContain('points="0 0 1 1"');
});

test('apend zeros if the list is short', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])
        ->withMaxItemAmount(10)
        ->make();

    expect($svg)->toContain('points="0 0 1 1 2 2 3 3 4 4 5 0 6 0 7 0 8 0 9 0"');
});

test('order reversed set to true', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])
        ->withOrderReversed()
        ->make();

    expect($svg)->toContain('points="0 4 1 3 2 2 3 1 4 0"');
});

test('order reversed set to false', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])
        ->withOrderReversed(false)
        ->make();

    expect($svg)->toContain('points="0 0 1 1 2 2 3 3 4 4"');
});

test('cut down the number of reversed items', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])
        ->withOrderReversed()
        ->withMaxItemAmount(3)
        ->make();

    expect($svg)->toContain('points="0 4 1 3 2 2"');
});

test('apend zeros if the list is short in reversed list', function (): void {
    $svg = LineChart::new([0, 1, 2, 3, 4])
        ->withOrderReversed()
        ->withMaxItemAmount(10)
        ->make();

    expect($svg)->toContain('points="0 4 1 3 2 2 3 1 4 0 5 0 6 0 7 0 8 0 9 0"');
});
