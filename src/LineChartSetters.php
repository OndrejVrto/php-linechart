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

    /** @var string[] */
    private array $colors = [];

    /** @var string[] */
    private array $defaultColors = ['#fbd808', '#ff9005', '#f9530b', '#ff0000'];

    public function withDimensions(mixed $widthSvg = null, mixed $heightSvg = null): self {
        $clone = clone $this;
        $clone->widthSvg = $this->positiveIntOrNull($widthSvg) ?? $clone->widthSvg;
        $clone->heightSvg = $this->positiveIntOrNull($heightSvg) ?? $clone->heightSvg;

        return $clone;
    }

    public function withStrokeWidth(mixed $strokeWidth = null): self {
        $clone = clone $this;
        $clone->strokeWidth = $this->positiveFloatOrNull($strokeWidth) ?? $clone->strokeWidth;

        return $clone;
    }

    public function withLockYAxisRange(mixed $lockValueY = null): self {
        $clone = clone $this;
        $clone->lockValueY = $this->positiveFloatOrNull($lockValueY) ?? $clone->lockValueY;

        return $clone;
    }

    public function withOrderReversed(): self {
        $clone = clone $this;
        $clone->reverseOrder = true;

        return $clone;
    }

    public function withMaxItemAmount(mixed $maxItemAmount = null): self {
        $clone = clone $this;
        $clone->maxItemAmount = $this->positiveIntOrNull($maxItemAmount) ?? $clone->maxItemAmount;

        return $clone;
    }

    public function withColorGradient(string ...$colorsInHex): self {
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
