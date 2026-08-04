<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator;

use Closure;
use Vaened\DeltaOrchestrator\Bindings\Behavior;

/**
 * @template TField of Field|Behavior
 */
final readonly class Scope
{
    /**
     * @param array<int, TField> $fields
     */
    private function __construct(private array $fields)
    {
    }

    /**
     * @param array<int, TField> $fields
     * @return self<TField>
     */
    public static function from(array $fields): self
    {
        return new self($fields);
    }

    /**
     * @param Closure(Field ...$fields): mixed $applicable
     */
    public function apply(Closure $applicable): Action
    {
        return Action::from($this->fields, $applicable);
    }
}
