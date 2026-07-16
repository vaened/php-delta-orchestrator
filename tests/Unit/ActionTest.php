<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Unit;

use RuntimeException;
use Vaened\DeltaOrchestrator\Action;
use Vaened\DeltaOrchestrator\ActionFailure;
use Vaened\DeltaOrchestrator\Exceptions\InvalidActionDefinition;
use Vaened\DeltaOrchestrator\Field;
use Vaened\DeltaOrchestrator\Rules\Rule;
use Vaened\DeltaOrchestrator\Tests\TestCase;

use function Vaened\DeltaOrchestrator\Rules\all;

final class ActionTest extends TestCase
{
    public function test_it_requires_at_least_one_field(): void
    {
        $this->expectException(InvalidActionDefinition::class);
        $this->expectExceptionMessage('Invalid action definition: Action fields cannot be empty.');

        new Action(
            fields: [],
            apply : static function (Field ...$fields): void {
            },
        );
    }

    public function test_it_exposes_description(): void
    {
        $action = new Action(
            fields     : [$this->field(value: 'Juan')],
            apply      : static function (Field ...$fields): void {
            },
            description: 'Update user profile',
        );

        self::assertSame('Update user profile', $action->description());
    }

    public function test_it_can_be_created_from_named_constructor(): void
    {
        $field  = $this->field(value: 'Juan');
        $action = Action::from(
            fields: [$field],
            apply : static function (Field ...$fields): void {
            },
        );

        self::assertSame([$field], $action->fields());
    }

    public function test_it_can_be_described_fluently(): void
    {
        $action = Action::from(
            fields: [$this->field(value: 'Juan')],
            apply : static function (Field ...$fields): void {
            },
        );

        $described = $action->describe('Update user profile');

        self::assertSame($action, $described);
        self::assertSame('Update user profile', $action->description());
    }

    public function test_it_can_define_when_condition_fluently(): void
    {
        $action = Action::from(
            fields: [$this->field(value: 'Juan')],
            apply : static function (Field ...$fields): void {
            },
        );

        $conditioned = $action->when(
            static function (Field $field): Rule {
                return all([$field]);
            },
        );

        self::assertSame($action, $conditioned);
        self::assertNotNull($action->condition());
    }

    public function test_it_can_define_custom_failure_fluently(): void
    {
        $action = Action::from(
            fields: [$this->field(value: 'Juan')],
            apply : static function (Field ...$fields): void {
            },
        );

        $configured = $action->or(
            static function (ActionFailure $failure): RuntimeException {
                return new RuntimeException($failure->getMessage(), previous: $failure);
            },
        );

        self::assertSame($action, $configured);
        self::assertNotNull($action->failureFactory());
    }
}
