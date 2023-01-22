<?php

declare(strict_types=1);

namespace OndrejVrto\LineChart;

use stdClass;
use Exception;
use Stringable;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Collection;
use Spatie\Color\Factory as ColorFactory;
use Spatie\Color\Exceptions\InvalidColorValue;

final class LineChart implements Stringable {
    use LineChartSetters;

    /** @var Collection<int,int|float> */
    private Collection $cleanData;

    /** @param null|array<mixed>|Collection<mixed> $data */
    public function __construct(
        private readonly null|array|Collection $data
    ) {
    }

    /** @param null|array<mixed>|Collection<mixed> $data */
    public static function new(null|array|Collection $data): self {
        return new self($data);
    }

    public function make(): string {
        $this->cleanData = $this->cleanInputData();

        $widthRaw = $this->resolveWidth();
        $heightRaw = $this->resolveHeight();

        $id = Uuid::uuid4()->toString();

        $points = $this->resolvePoints();
        $colors = $this->resolveColors();

        $widthSvg = $this->widthSvg;
        $heightSvg = $this->heightSvg;
        $strokeWidth = round($this->strokeWidth, 2);

        $widthScale = round(($widthSvg - $strokeWidth) / $widthRaw, 4);
        $heightScale = round(($heightSvg - $strokeWidth) / $heightRaw, 4);

        $widthTranslate = round($strokeWidth / ($widthScale * 2), 4);
        $heightTranslate = round(($strokeWidth / ($heightScale * 2)) + $heightRaw, 4);

        ob_start();
        include __DIR__.'/LineChart.view.php';
        $svg = ob_get_contents();
        ob_end_clean();

        return is_string($svg) ? $svg : '';
    }

    private function resolveWidth(): int {
        /** @var positive-int */
        $maxKey = $this->cleanData->keys()->pop();

        return max(1, (int) $maxKey);
    }

    private function resolveHeight(): int {
        /** @var float */
        $max = $this->lockValueY ?? $this->cleanData->max();

        return max(1, (int) $max);
    }

    public function resolvePoints(): string {
        return $this->cleanData
            ->map(function (int|float $value, int $key): string {
                $format = is_int($value) ? '%d' : '%01.2f';
                return sprintf("%d {$format}", $key, $value);
            })
            ->implode(' ');
    }

    /** @return Collection<int,stdClass> */
    private function resolveColors(): Collection {
        return collect($this->colors)
            ->map(function ($value) {
                try {
                    return (string) ColorFactory::fromString($value)->toHex();
                } catch (InvalidColorValue) {
                }

                return DefaultBrowsersColorNames::hexOrNull($value);
            })
            ->whereNotNull()
            ->whenEmpty(fn (Collection $collection): Collection => $collection->push(...$this->defaultColors))
            ->values()
            ->pipe(function (Collection $collection): Collection {
                $count = $collection->count();
                $step = 1 === $count || 0 === $count
                    ? 100
                    : 100 / ($count - 1);

                return $collection
                    ->map(function (mixed $colorHex, int $key) use ($step) {
                        $color = new stdClass();
                        $color->code = is_string($colorHex) ? mb_strtolower($colorHex) : '';
                        $color->offset = sprintf("%01.02h", ceil($key * $step) / 100);

                        return $color;
                    });
            });
    }

    /** @return Collection<int,int|float> */
    private function cleanInputData(): Collection {
        return collect($this->data)
            ->flatten()
            ->map(fn (mixed $value): int|float|null => match (true) {
                is_int($value)     => (int) $value,
                is_bool($value)    => (int) $value,
                is_numeric($value) => (float) $value,
                (is_string($value) && ctype_digit($value)) => (float) $value,
                default => null,
            })
            ->whereNotNull()
            ->when(
                $this->reverseOrder,
                fn (Collection $collection): Collection => $collection->reverse()
            )
            ->pipe(function (Collection $collection): Collection {
                $count = $collection->count();
                $max   = $this->maxItemAmount;
                $diff  = $count - $max;

                return match (true) {
                    0 === $count  => $collection->push(0, 0),
                    1 === $count  => $collection->prepend(0),
                    2 === $count  => $collection,
                    null === $max => $collection,
                    0 === $diff   => $collection,
                    $diff > 0     => $collection->take(max(2, $max)),
                    default       => $collection->pad($max, 0)
                };
            })
            ->pipe(function (Collection $collection): Collection {
                $min = $collection->min();

                return is_numeric($min) && $min < 0
                    ? $collection->map(fn (mixed $value): int|float => $value - $min)
                    : $collection;
            })
            ->values()
            ->map(fn (mixed $value): int|float => match (true) {
                is_int($value)     => (int) $value,
                is_numeric($value) => (float) $value,
                default            => throw new Exception(),
            });
    }

    public function __toString(): string {
        return $this->make();
    }
}
