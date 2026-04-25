<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Rules;

final readonly class Any implements Rule
{
    /**
     * @param array<int, Rule> $rules
     */
    public function __construct(
        private array $rules,
    ) {
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
