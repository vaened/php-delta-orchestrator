<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator;

use Closure;
use Vaened\DeltaOrchestrator\Bindings\Behavior;
use Vaened\DeltaOrchestrator\Rules\Rule;

final readonly class Action
{
    /**
     * @param array<int, Field|Behavior> $fields
     * @param Closure(Field ...$fields): Rule|null $when
     * @param Closure(Field ...$fields): mixed $apply
     */
    public function __construct(
        private array    $fields,
        private Closure  $apply,
        private ?Closure $when = null,
    )
    {
    }

    /**
     * @return array<int, Field|Behavior>
     */
    public function fields(): array
    {
        return $this->fields;
    }

    public function apply(): Closure
    {
        return $this->apply;
    }

    public function when(): ?Closure
    {
        return $this->when;
    }
}
