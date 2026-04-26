<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Unit;

use Vaened\DeltaOrchestrator\Action;
use Vaened\DeltaOrchestrator\Exceptions\InvalidActionDefinition;
use Vaened\DeltaOrchestrator\Field;
use Vaened\DeltaOrchestrator\Tests\TestCase;

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
            fields      : [$this->field(value: 'Juan')],
            apply       : static function (Field ...$fields): void {
            },
            description : 'Update user profile',
        );

        self::assertSame('Update user profile', $action->description());
    }
}
