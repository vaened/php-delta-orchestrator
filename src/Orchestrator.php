<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator;

use Vaened\DeltaOrchestrator\Bindings\Behavior;
use Vaened\DeltaOrchestrator\Exceptions\ActionBehaviorNotSatisfied;
use Vaened\DeltaOrchestrator\Rules\Rule;

final class Orchestrator
{
    /**
     * @var array<int, Action>
     */
    private array $actions = [];

    public function register(Action $action): self
    {
        $this->actions[] = $action;

        return $this;
    }

    public function execute(): void
    {
        foreach ($this->actions as $action) {
            $fields = $this->unwrap($action);

            if (!$this->passesWhen($action, $fields)) {
                continue;
            }

            if (!$this->passesBehaviors($action->fields())) {
                throw new ActionBehaviorNotSatisfied('Action behaviors were not satisfied.');
            }

            ($action->apply())(...$fields);
        }
    }

    /**
     * @param array<int, Field> $fields
     */
    private function passesWhen(Action $action, array $fields): bool
    {
        $when = $action->when();

        if ($when === null) {
            return true;
        }

        /** @var Rule $rule */
        $rule = $when(...$fields);

        return $rule->satisfies();
    }

    /**
     * @param array<int, Field|Behavior> $fields
     */
    private function passesBehaviors(array $fields): bool
    {
        foreach ($fields as $field) {
            if ($field instanceof Behavior && !$field->satisfies()) {
                return false;
            }
        }

        return true;
    }

    private function unwrap(Action $action): array
    {
        return array_map(
            static fn(Field|Behavior $field): Field => $field->field(),
            $action->fields(),
        );
    }
}
