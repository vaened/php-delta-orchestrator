<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Rules;

use Vaened\DeltaOrchestrator\Field;

final readonly class Any implements Rule
{
    use HasCompositeRules;

    /**
     * @var array<int, Rule>
     */
    private array $rules;

    /**
     * @param array<int, Field|Rule>|Field|Rule $rules
     */
    public function __construct(
        array|Field|Rule $rules,
    )
    {
        $this->rules = $this->compositeRules($rules);
    }

    public function satisfies(): bool
    {
        foreach ($this->rules as $rule) {
            if ($rule->satisfies()) {
                return true;
            }
        }

        return false;
    }
}
