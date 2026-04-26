<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Unit;

use Vaened\DeltaOrchestrator\Exceptions\ActionBehaviorNotSatisfied;
use Vaened\DeltaOrchestrator\Exceptions\InvalidActionDefinition;
use Vaened\DeltaOrchestrator\Tests\TestCase;

final class ExceptionsTest extends TestCase
{
    public function test_invalid_action_definition_builds_message_without_description(): void
    {
        $exception = new InvalidActionDefinition('Action fields cannot be empty');

        self::assertSame(
            'Invalid action definition: Action fields cannot be empty.',
            $exception->getMessage(),
        );
    }

    public function test_invalid_action_definition_builds_message_with_description(): void
    {
        $exception = new InvalidActionDefinition(
            reason     : 'Action fields cannot be empty',
            description: 'Update user profile',
        );

        self::assertSame(
            'Invalid action definition: Action fields cannot be empty (Update user profile).',
            $exception->getMessage(),
        );
    }

    public function test_action_behavior_not_satisfied_builds_message_without_description(): void
    {
        $field     = $this->field(value: null, current: 'Pedro', present: true);
        $exception = new ActionBehaviorNotSatisfied(
            behavior: $field->required(),
            field   : $field,
        );

        self::assertSame('Action behavior was not satisfied.', $exception->getMessage());
    }
}
