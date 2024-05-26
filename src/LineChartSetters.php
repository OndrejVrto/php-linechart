<?php

declare(strict_types=1);

namespace OndrejVrto\LineChart;

trait LineChartSetters {
    private int $widthSvg = 200;

    private int $heightSvg = 30;

    private float $strokeWidth = 2;

    private bool $reverseOrder = false;

    private ?int $maxItemAmount = null;

    private ?float $lockValueY = null;

    /** @var array<int|string,string|null>|null */
    private ?array $colors = null;

    /** @var string[] */
    private array $defaultColors = ['#fbd808', '#ff9005', '#f9530b', '#ff0000'];

    /**
     * Will determine the width and height of the rendered SVG.
     */
    public function withDimensions(mixed $widthSvg = null, mixed $heightSvg = null): self {
        $clone = clone $this;
        $clone->widthSvg = $this->positiveIntOrNull($widthSvg) ?? $clone->widthSvg;
        $clone->heightSvg = $this->positiveIntOrNull($heightSvg) ?? $clone->heightSvg;

        return $clone;
    }

    /**
     * Will determine the stroke's width
     */
    public function withStrokeWidth(mixed $strokeWidth = null): self {
        $clone = clone $this;
        $clone->strokeWidth = $this->positiveFloatOrNull($strokeWidth) ?? $clone->strokeWidth;

        return $clone;
    }

    /**
     * Sets the maximum value of the vertical axis. This is useful if you have
     * multiple charts that should have the same length vertical scale.
     * By default, the maximum value is determined based on the input values.
     */
    public function withLockYAxisRange(mixed $lockValueY = null): self {
        $clone = clone $this;
        $clone->lockValueY = $this->positiveFloatOrNull($lockValueY) ?? $clone->lockValueY;

        return $clone;
    }

    /**
     * Reverses the order of values
     */
    public function withOrderReversed(bool $status = true): self {
        $clone = clone $this;
        $clone->reverseOrder = $status;

        return $clone;
    }

    /**
     * Will determine how many values will be shown. If you originally passed
     * on more values than this max, then the oldest ones will be omitted.
     * If the max amount is set to a number that's higher than the current amount,
     * then the graph will extended.
     */
    public function withMaxItemAmount(mixed $maxItemAmount = null): self {
        $clone = clone $this;
        $clone->maxItemAmount = $this->positiveIntOrNull($maxItemAmount) ?? $clone->maxItemAmount;

        return $clone;
    }

    /**
     * You can choose any number of colors. A gradient for the graph is automatically generated from them.
     */
    public function withColorGradient(?string ...$colorsInHex): self {
        $clone = clone $this;
        $clone->colors = $colorsInHex;

        return $clone;
    }

    private function positiveIntOrNull(mixed $value): ?int {
        return is_numeric($value) || is_bool($value) || (is_string($value) && ctype_digit($value))
            ? (int) abs((int) $value)
            : null;
    }

    private function positiveFloatOrNull(mixed $value): ?float {
        return is_numeric($value) || is_bool($value) || (is_string($value) && ctype_digit($value))
            ? (float) abs((float) $value)
            : null;
    }
}
