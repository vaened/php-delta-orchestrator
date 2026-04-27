<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Unit;

use Vaened\DeltaOrchestrator\Action;
use Vaened\DeltaOrchestrator\Bindings\Behavior;
use Vaened\DeltaOrchestrator\Exceptions\ActionBehaviorNotSatisfied;
use Vaened\DeltaOrchestrator\Field;
use Vaened\DeltaOrchestrator\Orchestrator;
use Vaened\DeltaOrchestrator\Rules\Rule;
use Vaened\DeltaOrchestrator\Tests\TestCase;

use function Vaened\DeltaOrchestrator\Rules\all;

final class OrchestratorTest extends TestCase
{
    public function test_it_skips_action_when_when_rule_does_not_satisfy(): void
    {
        $executed = false;

        $action = new Action(
            fields: [$this->field(value: 'Juan', present: false)],
            apply : static function (Field ...$fields) use (&$executed): void {
                $executed = true;
            },
            when  : static function (Field $name): Rule {
                return all([$name]);
            },
        );

        (new Orchestrator())->register($action)->execute();

        self::assertFalse($executed);
    }

    public function test_it_executes_when_any_field_is_present_and_changed_by_default(): void
    {
        $executed = false;

        $action = new Action(
            fields: [
                $this->field(value: 'Juan', current: 'Pedro', present: true),
                $this->field(value: null, current: '1990-10-20', present: false)->optional(),
            ],
            apply : static function (Field ...$fields) use (&$executed): void {
                $executed = true;
            },
        );

        (new Orchestrator())->register($action)->execute();

        self::assertTrue($executed);
    }

    public function test_it_skips_action_when_fields_are_present_but_no_real_delta_exists(): void
    {
        $executed = false;

        $action = new Action(
            fields: [$this->field(value: 'Pedro', current: 'Pedro', present: true)],
            apply : static function (Field ...$fields) use (&$executed): void {
                $executed = true;
            },
        );

        (new Orchestrator())->register($action)->execute();

        self::assertFalse($executed);
    }

    public function test_it_throws_action_behavior_not_satisfied_with_context(): void
    {
        $field  = $this->field(value: null, current: 'Pedro', present: true);
        $action = new Action(
            fields      : [$field->required()],
            apply       : static function (Field ...$fields): void {
            },
            description : 'Update user profile',
        );

        try {
            (new Orchestrator())->register($action)->execute();

            self::fail('Expected ActionBehaviorNotSatisfied to be thrown.');
        } catch (ActionBehaviorNotSatisfied $exception) {
            self::assertSame('Update user profile', $exception->actionDescription());
            self::assertSame($field, $exception->field());
            self::assertInstanceOf(Behavior::class, $exception->behavior());
            self::assertSame(
                'Action behavior was not satisfied: Update user profile.',
                $exception->getMessage(),
            );
        }
    }
}
