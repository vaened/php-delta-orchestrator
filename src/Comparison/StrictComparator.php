<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Comparison;

use DateTimeInterface;
use Vaened\DeltaOrchestrator\Exceptions\ComparisonTypeMismatch;

final readonly class StrictComparator implements Comparator
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

        if ($value instanceof DateTimeInterface && $current instanceof DateTimeInterface) {
            return $value->format('Y-m-d H:i:s.uP') === $current->format('Y-m-d H:i:s.uP');
        }

        if (get_debug_type($value) !== get_debug_type($current)) {
            throw ComparisonTypeMismatch::forStrict($value, $current);
        }

        return $value === $current;
    }
}
