<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Integration;

use Vaened\DeltaOrchestrator\Action;
use Vaened\DeltaOrchestrator\Exceptions\ActionBehaviorNotSatisfied;
use Vaened\DeltaOrchestrator\Exceptions\StrictComparisonTypeMismatch;
use Vaened\DeltaOrchestrator\Field;
use Vaened\DeltaOrchestrator\Orchestrator;
use Vaened\DeltaOrchestrator\Patch\StringPatchValue;
use Vaened\DeltaOrchestrator\Tests\Support\CreatesIntegrationScenarios;
use Vaened\DeltaOrchestrator\Tests\TestCase;

final class OrchestratorFailureTest extends TestCase
{
    use CreatesIntegrationScenarios;

    public function test_it_throws_action_behavior_not_satisfied_with_context(): void
    {
        $field = $this->singleValueField(
            value  : new StringPatchValue(true, null),
            current: 'Pedro',
        );

        $action = new Action(
            fields      : [$field->required()],
            apply       : static function (Field ...$fields): void {
            },
            when        : null,
            description : 'Update user profile',
        );

        try {
            (new Orchestrator())->register($action)->execute();

            self::fail('Expected ActionBehaviorNotSatisfied to be thrown.');
        } catch (ActionBehaviorNotSatisfied $exception) {
            self::assertSame('Update user profile', $exception->actionDescription());
            self::assertSame($field, $exception->field());
            self::assertSame(
                'Action behavior was not satisfied: Update user profile.',
                $exception->getMessage(),
            );
        }
    }

    public function test_it_throws_when_default_strict_comparison_finds_mismatched_types(): void
    {
        $field = $this->singleValueField(
            value  : new StringPatchValue(true, '10'),
            current: 10,
        );

        $action = new Action(
            fields: [$field],
            apply : static function (Field ...$fields): void {
            },
            when  : null,
        );

        $this->expectException(StrictComparisonTypeMismatch::class);

        (new Orchestrator())->register($action)->execute();
    }
}
