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
 *
 * @implements Behavior<TValue, TCurrent>
 */
final readonly class Optional implements Behavior
{
    /**
     * @param Field<TValue, TCurrent> $field
     */
    public function __construct(
        private Field $field,
    ) {
    }

    /**
     * @return Field<TValue, TCurrent>
     */
    public function field(): Field
    {
        return $this->field;
    }

    public function satisfies(): bool
    {
        return true;
    }
}
