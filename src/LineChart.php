<?php

declare(strict_types=1);

namespace OndrejVrto\LineChart;

use Exception;
use Stringable;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Collection;

final class LineChart implements Stringable {
    use LineChartSetters;

    private Collection $cleanData;

    /** @param array<mixed> $data */
    public function __construct(
        private readonly array $data
    ) {
    }

    /** @param array<mixed> $data */
    public static function new(array $data): self {
        return new self($data);
    }

    public function make(): string {
        $this->cleanInputData();

        $widthRaw = $this->resolveWidth();
        $heightRaw = $this->resolveHeight();

        $id = Uuid::uuid4()->toString();

        $points = $this->resolvePoints();
        $colors = $this->resolveColors();

        $widthSVG = $this->widthSvg;
        $heightSVG = $this->heightSvg;
        $strokeWidth = $this->strokeWidth;

        $widthScale = round(($this->widthSvg - $this->strokeWidth) / $widthRaw, 4);
        $heightScale = round(($this->heightSvg - $this->strokeWidth) / $heightRaw, 4);

        $widthTranslate = round($strokeWidth / ($widthScale * 2), 4);
        $heightTranslate = round(($strokeWidth / ($heightScale * 2)) + $heightRaw, 4);

        ob_start();
        include __DIR__.'/LineChart.view.php';
        $svg = ob_get_contents();
        ob_end_clean();

        return is_string($svg) ? $svg : '';
    }

    public function __toString(): string {
        return $this->make();
    }

    private function cleanInputData(): void {
        $tmp = collect($this->data)
            ->flatten()
            ->filter(fn ($value) => is_numeric($value) || (is_string($value) && ctype_digit($value)) || is_bool($value));

        if (0 === $tmp->count()) {
            throw new Exception('Bad input data.');
        }

        $tmp = $tmp
            ->map(fn ($value) => (float) $value)
            ->when(
                $this->reverseOrder,
                fn ($collection) => $collection->reverse()
            )
            ->unless(
                null === $this->maxItemAmount,
                fn ($collection) => $collection->shift($this->maxItemAmount)
            );

        $min = $tmp->min();

        $this->cleanData = $tmp
            ->when(
                $min < 0,
                fn ($collection) => $collection->map(fn ($value) => $value - $min)
            )
            ->values();
    }

    public function resolvePoints(): string {
        return $this->cleanData
            ->map(fn (float $value, int $key) => sprintf("%d %h", $key, $value))
            ->implode(' ');
    }

    private function resolveWidth(): int {
        return max(1, (int) $this->cleanData->keys()->pop());
    }

    private function resolveHeight(): int {
        return max(1, (int) ceil($this->cleanData->max()));
    }

    /** @return string[] */
    private function resolveColors(): array {
        $percentageStep = 100 / (count($this->colors) - 1);

        $colorsWithPercentage = [];
        foreach ($this->colors as $key => $color) {
            $newKey = ceil($key * $percentageStep);
            $colorsWithPercentage[$newKey] = mb_strtolower($color);
        }

        return $colorsWithPercentage;
    }
}
