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
 * @template TResolved
 * @template TCurrent
 *
 * @implements Behavior<TValue, TResolved, TCurrent>
 */
final readonly class Required implements Behavior
{
    /**
     * @param Field<TValue, TResolved, TCurrent> $field
     */
    public function __construct(
        private Field $field,
    ) {
    }

    /**
     * @return Field<TValue, TResolved, TCurrent>
     */
    public function field(): Field
    {
        return $this->field;
    }

    public function satisfies(): bool
    {
        return $this->field->value() !== null;
    }
}
