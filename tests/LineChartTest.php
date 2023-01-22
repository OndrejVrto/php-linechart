<?php

declare(strict_types=1);

use OndrejVrto\LineChart\LineChart;

test('stringable interface', function (): void {
    $svg = (string) new LineChart([0, 1, 2, 3, 4]);

    $this->assertStringContainsString('<svg', $svg);
});

it('create svg from static method', function (): void {
    $svg = $this->lineChart
        ->make();

    $this->assertStringContainsString('<svg', $svg);
    $this->assertStringContainsString('points="0 0 1 1 2 2 3 3 4 4"', $svg);
});

it('default stroke', function (): void {
    $svg = $this->lineChart
        ->make();

    $this->assertStringContainsString('stroke-width="2"', $svg);
});

test('set stroke to long float value', function (): void {
    $svg = $this->lineChart
        ->withStrokeWidth(10.5555555555)
        ->make();

    $this->assertStringContainsString('stroke-width="10.5556"', $svg);
});

test('set stroke to negative value', function (): void {
    $svg = $this->lineChart
        ->withStrokeWidth(-5)
        ->make();

    $this->assertStringContainsString('stroke-width="5"', $svg);
});

test('lock Y axis', function (): void {
    $svg = $this->lineChart
        ->withStrokeWidth(20)  // set stroke to 20 for preety scale number
        ->withLockYAxisRange(10)
        ->make();

    $this->assertStringContainsString('transform="scale(45 -1) translate(0.2222 -20)"', $svg);
});

test('cut down on the number of items', function (): void {
    $svg = $this->lineChart
        ->withMaxItemAmount(3)
        ->make();

    $this->assertStringContainsString('points="0 0 1 1 2 2"', $svg);
});

test('cut down the number of items if the list is short', function (): void {
    $svg = $this->lineChart
        ->withMaxItemAmount(10)
        ->make();

    $this->assertStringContainsString('points="0 0 1 1 2 2 3 3 4 4"', $svg);
});

test('order reversed set to true', function (): void {
    $svg = $this->lineChart
        ->withOrderReversed()
        ->make();

    $this->assertStringContainsString('points="0 4 1 3 2 2 3 1 4 0"', $svg);
});

test('cut down the number of reversed items', function (): void {
    $svg = $this->lineChart
        ->withOrderReversed()
        ->withMaxItemAmount(3)
        ->make();

    $this->assertStringContainsString('points="0 4 1 3 2 2"', $svg);
});

it('default size', function (): void {
    $svg = $this->lineChart
        ->make();

    $this->assertStringContainsString('viewBox="0 0 200 30"', $svg);
    $this->assertStringContainsString('width="200"', $svg);
    $this->assertStringContainsString('height="30"', $svg);
});

test('change sizes', function (): void {
    $svg = $this->lineChart
        ->withDimensions(1000, 100)
        ->make();

    $this->assertStringContainsString('viewBox="0 0 1000 100"', $svg);
    $this->assertStringContainsString('width="1000"', $svg);
    $this->assertStringContainsString('height="100"', $svg);
});

test('change only the width using a negative float number', function (): void {
    $svg = $this->lineChart
        ->withDimensions(-333.1234)
        ->make();

    $this->assertStringContainsString('viewBox="0 0 333 30"', $svg);
    $this->assertStringContainsString('width="333"', $svg);
    $this->assertStringContainsString('height="30"', $svg);
});

it('default four colors', function (): void {
    $svg = $this->lineChart
        ->make();

    $this->assertStringContainsString('<stop stop-color="#fbd808" offset="0"></stop>', $svg);
    $this->assertStringContainsString('<stop stop-color="#ff9005" offset="0.34"></stop>', $svg);
    $this->assertStringContainsString('<stop stop-color="#f9530b" offset="0.67"></stop>', $svg);
    $this->assertStringContainsString('<stop stop-color="#ff0000" offset="1"></stop>', $svg);
});

test('three color in default browser text format', function (): void {
    $svg = $this->lineChart
        ->withColorGradient('Green', 'Orange', 'Red')
        ->make();

    $this->assertStringContainsString('stop-color="#008000" offset="0"', $svg);
    $this->assertStringContainsString('stop-color="#ffa500" offset="0.5"', $svg);
    $this->assertStringContainsString('stop-color="#ff0000" offset="1"', $svg);
});

test('color in format', function (string $color): void {
    $svg = $this->lineChart
        ->withColorGradient($color)
        ->make();

    $this->assertStringContainsString('stop-color="#0000ff"', $svg);
})->with([
    'hex'  => '#0000ff',
    'rgb'  => 'rgb(0, 0, 255)',
    'rgba' => 'rgba(0, 0, 255, 1)',
    'hsla' => 'hsla(240, 100%, 50%, 1)',
    'hsl'  => 'hsl(240, 100%, 50%)',
    'default browser text' => 'blue',
]);

test('setting the default value if a non-existent color format is required', function (): void {
    $svg = $this->lineChart
        ->withColorGradient('foo', 'bar')
        ->make();

    $this->assertStringContainsString('<stop stop-color="#fbd808" offset="0"></stop>', $svg);
    $this->assertStringContainsString('<stop stop-color="#ff9005" offset="0.34"></stop>', $svg);
    $this->assertStringContainsString('<stop stop-color="#f9530b" offset="0.67"></stop>', $svg);
    $this->assertStringContainsString('<stop stop-color="#ff0000" offset="1"></stop>', $svg);
});

test('input data conversion', function (mixed $input, string $result): void {
    $svgChart = LineChart::new($input)->make();

    $this->assertStringContainsString('points="'.$result.'"', $svgChart);
})->with([
    'integer values greater than zero' => [
        [0, 1, 2, 3, 4],
        '0 0 1 1 2 2 3 3 4 4'
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
    'conbinations' => [
        [-5, 0, true, false, 5.55555, 1000],
        '0 0 1 5 2 6 3 5 4 10.56 5 1005'
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
        null,
        '0 0 1 0'
    ],
    'bad values' => [
        ['foo', 'bar'],
        '0 0 1 0'
    ],
]);
