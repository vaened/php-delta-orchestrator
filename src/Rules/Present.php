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
    private array $fields;

    /**
     * @param array<int, Field>|Field $fields
     */
    public function __construct(
        array|Field $fields,
    )
    {
        $this->fields = $fields instanceof Field ? [$fields] : $fields;
    }

    public function satisfies(): bool
    {
        foreach ($this->fields as $field) {
            if (!$field->isPresent()) {
                return false;
            }
        }

        return true;
    }
}
