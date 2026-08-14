<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Unit;

use RuntimeException;
use Vaened\DeltaOrchestrator\ActionFailure;
use Vaened\DeltaOrchestrator\Exceptions\ActionBehaviorNotSatisfied;
use Vaened\DeltaOrchestrator\Exceptions\ComparisonTypeMismatch;
use Vaened\DeltaOrchestrator\Exceptions\InvalidActionDefinition;
use Vaened\DeltaOrchestrator\Tests\TestCase;

final class ExceptionsTest extends TestCase
{
    public function test_invalid_action_definition_builds_message_without_description(): void
    {
        $exception = InvalidActionDefinition::emptyFields();

        self::assertSame(
            'Invalid action definition: Action fields cannot be empty.',
            $exception->getMessage(),
        );
    }

    public function test_invalid_action_definition_builds_message_with_description(): void
    {
        $exception = InvalidActionDefinition::emptyFields('Update user profile');

        self::assertSame(
            'Invalid action definition: Action fields cannot be empty (Update user profile).',
            $exception->getMessage(),
        );
    }

    public function test_invalid_action_definition_builds_when_result_message(): void
    {
        $exception = InvalidActionDefinition::unexpectedWhenResult('Update user profile');

        self::assertSame(
            'Invalid action definition: Action when condition must return a Rule (Update user profile).',
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
        self::assertNull($exception->progressUntilFailure());
    }

    public function test_action_behavior_not_satisfied_can_rethrow_custom_failure(): void
    {
        $field     = $this->field(value: null, current: 'Pedro', present: true);
        $exception = new ActionBehaviorNotSatisfied(
            behavior      : $field->required(),
            field         : $field,
            failureFactory: static function (ActionFailure $failure): RuntimeException {
                return new RuntimeException('Custom failure', previous: $failure);
            },
        );

        try {
            $exception->rethrow();
        } catch (RuntimeException $custom) {
            self::assertSame('Custom failure', $custom->getMessage());
            self::assertSame($exception, $custom->getPrevious());
        }
    }

    public function test_strict_comparison_type_mismatch_builds_message(): void
    {
        $exception = ComparisonTypeMismatch::forStrict('10', 10);

        self::assertSame(
            'Strict comparison requires matching types. Got <string> and <int>.',
            $exception->getMessage(),
        );
    }

    public function test_datetime_comparison_type_mismatch_builds_message(): void
    {
        $exception = ComparisonTypeMismatch::forDateTime('not-a-date', 10);

        self::assertSame(
            'DateTime comparison requires DateTimeInterface or parseable date strings. Got <string> and <int>.',
            $exception->getMessage(),
        );
    }
}
