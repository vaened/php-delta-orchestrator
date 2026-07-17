<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Rules;

use Closure;
use Vaened\DeltaOrchestrator\Field;

final readonly class All implements Rule
{
    use HasCompositeRules;

    /**
     * @return Closure(Field ...$fields): All
     */
    public static function isPresent(): Closure
    {
        return static fn(Field ...$fields): self => new self($fields);
    }

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
            if (!$rule->satisfies()) {
                return false;
            }
        }

        return true;
    }
}
