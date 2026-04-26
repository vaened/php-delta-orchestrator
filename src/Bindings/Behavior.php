<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Bindings;

use Vaened\DeltaOrchestrator\Field;

/**
 * @template TValue
 * @template TCurrent
 */
interface Behavior
{
    /**
     * @return Field<TValue, TCurrent>
     */
    public function field(): Field;

    public function satisfies(): bool;
}
