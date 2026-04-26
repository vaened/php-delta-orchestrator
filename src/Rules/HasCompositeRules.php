<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Rules;

use Vaened\DeltaOrchestrator\Field;

trait HasCompositeRules
{
    /**
     * @param array<int, Field|Rule>|Field|Rule $rules
     *
     * @return array<int, Rule>
     */
    private function compositeRules(array|Field|Rule $rules): array
    {
        $items = is_array($rules) ? $rules : [$rules];

        return array_map(
            static fn(Field|Rule $item): Rule => $item instanceof Field
                ? new Present($item)
                : $item,
            $items,
        );
    }
}
