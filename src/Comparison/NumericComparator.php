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
    use HandlesNullComparison;

    public static function create(): self
    {
        return new self();
    }

    public function equals(mixed $value, mixed $current): bool
    {
        $equals = $this->compareNulls($value, $current);

        if ($equals !== null) {
            return $equals;
        }

        $rawValue   = $value;
        $rawCurrent = $current;
        $value      = $this->normalize($value);
        $current    = $this->normalize($current);

        if ($value === null || $current === null) {
            throw ComparisonTypeMismatch::forNumeric($rawValue, $rawCurrent);
        }

        return $value === $current;
    }

    private function normalize(mixed $value): ?string
    {
        $value = $this->toComparable($value);

        if ($value === null) {
            return null;
        }

        return $this->canonicalize($value);
    }

    private function toComparable(mixed $value): ?string
    {
        if (is_int($value)) {
            return (string)$value;
        }

        if (is_float($value)) {
            return (string)$value;
        }

        if (!is_string($value)) {
            return null;
        }

        return trim($value);
    }

    private function canonicalize(string $value): ?string
    {
        if ($value === '' || !is_numeric($value)) {
            return null;
        }

        if (str_contains($value, 'e') || str_contains($value, 'E')) {
            return null;
        }

        [$sign, $value] = $this->split($value);

        [$integer, $decimal] = array_pad(explode('.', $value, 2), 2, '');

        $integer = ltrim($integer, '0');
        $integer = $integer === '' ? '0' : $integer;
        $decimal = rtrim($decimal, '0');

        $normalized = $decimal === ''
            ? $integer
            : $integer . '.' . $decimal;

        if ($normalized === '0') {
            return '0';
        }

        return $sign . $normalized;
    }

    private function split(string $value): array
    {
        if ($value[0] !== '+' && $value[0] !== '-') {
            return ['', $value];
        }

        return [
            $value[0] === '-' ? '-' : '',
            substr($value, 1),
        ];
    }
}
