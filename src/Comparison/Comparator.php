<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Comparison;

/**
 * @template TValue
 * @template TCurrent
 */
interface Comparator
{
    /**
     * @param TValue $value
     * @param TCurrent $current
     */
    public function equals(mixed $value, mixed $current): bool;
}
