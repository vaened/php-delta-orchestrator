<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Rules;

use Vaened\DeltaOrchestrator\Field;

final readonly class Present implements Rule
{
    public function __construct(
        private Field $field,
    )
    {
    }

    public function satisfies(): bool
    {
        return $this->field->isPresent();
    }
}
