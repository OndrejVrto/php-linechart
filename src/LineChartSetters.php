<?php

declare(strict_types=1);

namespace OndrejVrto\LineChart;

trait LineChartSetters {
    private int $widthSvg = 200;

    private int $heightSvg = 30;

    private int $strokeWidth = 2;

    private bool $reverseOrder = false;

    private ?int $maxItemAmount = null;

    /** @var string[] */
    private array $colors = ['#ED7D31', '#E41A5C'];

    public function withDimensions(?int $widthSvg = null, ?int $heightSvg = null): self {
        $clone = clone $this;
        $clone->widthSvg = $this->positiveIntOrNull($widthSvg) ?? $clone->widthSvg;
        $clone->heightSvg = $this->positiveIntOrNull($heightSvg) ?? $clone->heightSvg;

        return $clone;
    }

    public function withStrokeWidth(?int $strokeWidth = null): self {
        $clone = clone $this;
        $clone->strokeWidth = $this->positiveIntOrNull($strokeWidth) ?? $clone->strokeWidth;

        return $clone;
    }

    public function withOrderReversed(): self {
        $clone = clone $this;
        $clone->reverseOrder = true;

        return $clone;
    }

    public function withMaxItemAmount(?int $maxItemAmount = null): self {
        $clone = clone $this;
        $clone->maxItemAmount = $this->positiveIntOrNull($maxItemAmount) ?? $clone->maxItemAmount;

        return $clone;
    }

    public function withColorGradient(string ...$colors): self {
        $clone = clone $this;
        $clone->colors = $colors;

        return $clone;
    }

    private function positiveIntOrNull(mixed $value): ?int {
        $value = match (true) {
            is_int($value) => $value,
            is_bool($value) => (int) $value,
            is_numeric($value) => (int) $value,
            (is_string($value) && ctype_digit($value)) => (int) $value,
            default => 0,
        };

        return abs($value) > 0 ? abs($value) : null;
    }
}
