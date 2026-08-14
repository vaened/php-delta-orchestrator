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

        [$value, $current] = $this->build($value, $current);

        return $value->format('U.u') === $current->format('U.u');
    }

    private function build(mixed $value, mixed $current): array
    {
        $resolvedValue   = $this->resolve($value);
        $resolvedCurrent = $this->resolve($current);

        if ($resolvedValue === null || $resolvedCurrent === null) {
            throw ComparisonTypeMismatch::forDateTime($value, $current);
        }

        return [$resolvedValue, $resolvedCurrent];
    }

    private function resolve(mixed $value): ?DateTimeInterface
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        return match (true) {
            is_string($value) => $this->fromString($value),
            default           => null,
        };
    }

    private function fromString(string $value): ?DateTimeInterface
    {
        try {
            return new DateTimeImmutable($value);
        } catch (Exception) {
            return null;
        }
    }
}
