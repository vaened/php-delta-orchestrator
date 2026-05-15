<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Comparison;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Vaened\DeltaOrchestrator\Exceptions\ComparisonTypeMismatch;

final readonly class DateTimeComparator implements Comparator
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

        $value   = $this->normalize($value, $current);
        $current = $this->normalize($current, $value);

        return $value->format('U.u') === $current->format('U.u');
    }

    private function normalize(mixed $value, mixed $other): DateTimeInterface
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        if (is_string($value)) {
            try {
                return new DateTimeImmutable($value);
            } catch (Exception) {
                throw ComparisonTypeMismatch::forDateTime($value, $other);
            }
        }

        throw ComparisonTypeMismatch::forDateTime($value, $other);
    }
}
