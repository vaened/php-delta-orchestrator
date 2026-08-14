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

    private const FLOAT_SCALE = 15;

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
            return sprintf('%.' . self::FLOAT_SCALE . 'F', $value);
        }

        if (!is_string($value)) {
            return null;
        }

        return trim($value);
    }

    private function canonicalize(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        $parts = $this->parse($value);

        if ($parts === null) {
            return null;
        }

        [$sign, $integer, $decimal, $exponent] = $parts;
        [$integer, $decimal] = $this->apply($integer, $decimal, $exponent);

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

    private function parse(string $value): ?array
    {
        $pattern = '/^([+-]?)(?:(\d+)(?:\.(\d*))?|\.(\d+))(?:[eE]([+-]?\d+))?$/';

        if (preg_match($pattern, $value, $matches) !== 1) {
            return null;
        }

        return [
            $matches[1] === '-' ? '-' : '',
            ($matches[2] ?? '') !== '' ? $matches[2] : '0',
            ($matches[3] ?? '') !== '' ? $matches[3] : ($matches[4] ?? ''),
            isset($matches[5]) ? (int)$matches[5] : 0,
        ];
    }

    private function apply(string $integer, string $decimal, int $exponent): array
    {
        $digits      = $integer . $decimal;
        $decimalAt   = strlen($integer) + $exponent;
        $digitsCount = strlen($digits);

        if ($decimalAt <= 0) {
            return [
                '0',
                str_repeat('0', -$decimalAt) . $digits,
            ];
        }

        if ($decimalAt >= $digitsCount) {
            return [
                $digits . str_repeat('0', $decimalAt - $digitsCount),
                '',
            ];
        }

        return [
            substr($digits, 0, $decimalAt),
            substr($digits, $decimalAt),
        ];
    }
}
