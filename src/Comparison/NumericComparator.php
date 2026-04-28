<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Comparison;

use Vaened\DeltaOrchestrator\Exceptions\ComparisonTypeMismatch;

final readonly class NumericComparator implements Comparator
{
    public static function create(): self
    {
        return new self();
    }

    public function equals(mixed $value, mixed $current): bool
    {
        $rawValue = $value;
        $rawCurrent = $current;
        $value = $this->normalize($value);
        $current = $this->normalize($current);

        if ($value === null || $current === null) {
            throw ComparisonTypeMismatch::forNumeric($rawValue, $rawCurrent);
        }

        return $value === $current;
    }

    private function normalize(mixed $value): ?float
    {
        if (is_int($value) || is_float($value)) {
            return (float)$value;
        }

        if (is_string($value) && is_numeric($value)) {
            return (float)$value;
        }

        return null;
    }
}
