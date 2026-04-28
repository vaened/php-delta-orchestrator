<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Unit;

use Vaened\DeltaOrchestrator\Field;
use Vaened\DeltaOrchestrator\Patch\StringPatchValue;
use Vaened\DeltaOrchestrator\Tests\TestCase;

final class FieldFactoryTest extends TestCase
{
    public function test_it_builds_field_from_direct_current_value(): void
    {
        $field = Field::from(
            patch  : new StringPatchValue(true, 'Juan'),
            current: 'Pedro',
        );

        self::assertTrue($field->isPresent());
        self::assertSame('Juan', $field->value());
        self::assertSame('Pedro', $field->current());
    }

    public function test_it_builds_field_from_lazy_current_callback(): void
    {
        $invocations = 0;

        $field = Field::from(
            patch  : new StringPatchValue(true, 'Juan'),
            current: function () use (&$invocations): string {
                $invocations++;

                return 'Pedro';
            },
        );

        self::assertSame(0, $invocations);
        self::assertSame('Pedro', $field->current());
        self::assertSame(1, $invocations);
        self::assertSame('Pedro', $field->current());
        self::assertSame(1, $invocations);
    }
}
