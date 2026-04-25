<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Bindings;

use Vaened\DeltaOrchestrator\Field;

final readonly class Optional implements Behavior
{
    public function __construct(
        private Field $field,
    ) {
    }

    public function field(): Field
    {
        return $this->field;
    }

    public function satisfies(): bool
    {
        return true;
    }
}
