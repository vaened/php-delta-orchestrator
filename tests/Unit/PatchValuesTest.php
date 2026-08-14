<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Unit;

use DateTime;
use DateTimeImmutable;
use Stringable;
use Vaened\DeltaOrchestrator\Exceptions\InvalidPatchValue;
use Vaened\DeltaOrchestrator\Patch\ArrayPatchValue;
use Vaened\DeltaOrchestrator\Patch\BoolPatchValue;
use Vaened\DeltaOrchestrator\Patch\DateTimeImmutablePatchValue;
use Vaened\DeltaOrchestrator\Patch\FloatPatchValue;
use Vaened\DeltaOrchestrator\Patch\IntPatchValue;
use Vaened\DeltaOrchestrator\Patch\StringPatchValue;
use Vaened\DeltaOrchestrator\Tests\TestCase;

final class PatchValuesTest extends TestCase
{
    public function test_string_patch_value_normalizes_scalar_and_stringable_values(): void
    {
        $value = new class implements Stringable {
            public function __toString(): string
            {
                return 'Pedro';
            }
        };

        self::assertSame('10', (new StringPatchValue(true, 10))->value());
        self::assertSame('10.5', (new StringPatchValue(true, 10.5))->value());
        self::assertSame('Pedro', (new StringPatchValue(true, $value))->value());
    }

    public function test_int_patch_value_normalizes_numeric_strings(): void
    {
        self::assertSame(10, (new IntPatchValue(true, '10'))->value());
        self::assertSame(-20, (new IntPatchValue(true, '-20'))->value());
    }

    public function test_it_rejects_invalid_int_patch_values(): void
    {
        $this->expectException(InvalidPatchValue::class);

        new IntPatchValue(true, '10.5');
    }

    public function test_float_patch_value_normalizes_numeric_strings_and_ints(): void
    {
        self::assertSame(10.5, (new FloatPatchValue(true, '10.5'))->value());
        self::assertSame(10.0, (new FloatPatchValue(true, 10))->value());
    }

    public function test_it_rejects_invalid_float_patch_values(): void
    {
        $this->expectException(InvalidPatchValue::class);

        new FloatPatchValue(true, 'hola');
    }

    public function test_bool_patch_value_normalizes_common_inputs(): void
    {
        self::assertTrue((new BoolPatchValue(true, 'true'))->value());
        self::assertTrue((new BoolPatchValue(true, 1))->value());
        self::assertFalse((new BoolPatchValue(true, '0'))->value());
        self::assertFalse((new BoolPatchValue(true, 'off'))->value());
    }

    public function test_it_rejects_invalid_bool_patch_values(): void
    {
        $this->expectException(InvalidPatchValue::class);

        new BoolPatchValue(true, 'hola');
    }

    public function test_datetime_patch_value_normalizes_strings_and_mutable_dates(): void
    {
        $stringValue = new DateTimeImmutablePatchValue(true, '2026-04-26 10:20:30');
        $mutableValue = new DateTimeImmutablePatchValue(true, new DateTime('2026-04-26 10:20:30'));

        self::assertInstanceOf(DateTimeImmutable::class, $stringValue->value());
        self::assertSame('2026-04-26 10:20:30', $stringValue->value()?->format('Y-m-d H:i:s'));
        self::assertSame('2026-04-26 10:20:30', $mutableValue->value()?->format('Y-m-d H:i:s'));
    }

    public function test_it_rejects_invalid_datetime_patch_values(): void
    {
        $this->expectException(InvalidPatchValue::class);

        new DateTimeImmutablePatchValue(true, 'no-date');
    }

    public function test_array_patch_value_normalizes_nested_patch_values(): void
    {
        $value = new ArrayPatchValue(true, [
            'name' => new StringPatchValue(true, 'Juan'),
            'meta' => [
                'age' => new IntPatchValue(true, '20'),
            ],
        ]);

        self::assertSame(
            [
                'name' => 'Juan',
                'meta' => ['age' => 20],
            ],
            $value->value(),
        );
    }

    public function test_patch_value_named_constructors_cover_generic_present_and_missing_cases(): void
    {
        self::assertSame('Juan', StringPatchValue::from(true, 'Juan')->value());
        self::assertTrue(StringPatchValue::from(true, 'Juan')->isPresent());

        self::assertSame(20, IntPatchValue::present('20')->value());
        self::assertTrue(IntPatchValue::present('20')->isPresent());

        self::assertNull(BoolPatchValue::missing()->value());
        self::assertFalse(BoolPatchValue::missing()->isPresent());
    }
}
