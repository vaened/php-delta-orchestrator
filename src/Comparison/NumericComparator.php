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

    private const FLOAT_SIGNIFICANT_DIGITS = PHP_FLOAT_DIG;
    private const MAX_EXACT_INTEGER_FLOAT = 2 ** 53;
    private const MAX_PARSE_EXPONENT = 1000000;

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
            if (!is_finite($value)) {
                return null;
            }

            if ($value === floor($value) && abs($value) <= self::MAX_EXACT_INTEGER_FLOAT) {
                return sprintf('%.0F', $value);
            }

            return sprintf('%.' . self::FLOAT_SIGNIFICANT_DIGITS . 'G', $value);
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

        [$sign, $digits, $exponent] = $parts;

        $digits = ltrim($digits, '0');

        if ($digits === '') {
            return '0';
        }

        $trailingZeros = strlen($digits) - strlen(rtrim($digits, '0'));
        $digits        = rtrim($digits, '0');
        $exponent     += $trailingZeros;

        // Internal canonical token: normalized mantissa digits plus exponent.
        return $sign . $digits . 'e' . $exponent;
    }

    /**
     * @return array{0: string, 1: string, 2: int}|null
     */
    private function parse(string $value): ?array
    {
        $pattern = '/^([+-]?)(?:(\d+)(?:\.(\d*))?|\.(\d+))(?:[eE]([+-]?\d+))?$/';

        if (preg_match($pattern, $value, $matches) !== 1) {
            return null;
        }

        $fraction = ($matches[3] ?? '') !== '' ? $matches[3] : ($matches[4] ?? '');
        $exponent = isset($matches[5]) ? (int)$matches[5] : 0;

        if ($exponent > self::MAX_PARSE_EXPONENT || $exponent < -self::MAX_PARSE_EXPONENT) {
            return null;
        }

        return [
            $matches[1] === '-' ? '-' : '',
            $matches[2] . $fraction,
            $exponent - strlen($fraction),
        ];
    }
}
