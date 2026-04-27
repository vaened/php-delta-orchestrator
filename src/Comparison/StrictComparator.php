<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Comparison;

use DateTimeInterface;
use Vaened\DeltaOrchestrator\Exceptions\StrictComparisonTypeMismatch;

final readonly class StrictComparator implements Comparator
{
    public static function create(): self
    {
        return new self();
    }

    public function equals(mixed $value, mixed $current): bool
    {
        if ($value instanceof DateTimeInterface && $current instanceof DateTimeInterface) {
            return $value->format('Y-m-d H:i:s.uP') === $current->format('Y-m-d H:i:s.uP');
        }

        if (get_debug_type($value) !== get_debug_type($current)) {
            throw new StrictComparisonTypeMismatch($value, $current);
        }

        return $value === $current;
    }
}
