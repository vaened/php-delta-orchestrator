<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Unit;

use DateTimeImmutable;
use Vaened\DeltaOrchestrator\Comparison\ArrayComparator;
use Vaened\DeltaOrchestrator\Exceptions\ComparisonTypeMismatch;
use Vaened\DeltaOrchestrator\Comparison\DateTimeComparator;
use Vaened\DeltaOrchestrator\Comparison\LooseComparator;
use Vaened\DeltaOrchestrator\Comparison\NumericComparator;
use Vaened\DeltaOrchestrator\Comparison\StrictComparator;
use Vaened\DeltaOrchestrator\Tests\TestCase;

final class ComparatorsTest extends TestCase
{
    public function test_strict_comparator_uses_strict_equality(): void
    {
        $comparator = StrictComparator::create();

        self::assertTrue($comparator->equals('10', '10'));
    }

    public function test_strict_comparator_throws_when_types_do_not_match(): void
    {
        $comparator = StrictComparator::create();

        $this->expectException(ComparisonTypeMismatch::class);

        $comparator->equals('10', 10);
    }

    public function test_strict_comparator_handles_nulls_without_exception(): void
    {
        $comparator = StrictComparator::create();

        self::assertTrue($comparator->equals(null, null));
        self::assertFalse($comparator->equals(null, '10'));
    }

    public function test_strict_comparator_compares_datetimes_by_exact_temporal_value(): void
    {
        $comparator = StrictComparator::create();

        $sameA = new DateTimeImmutable('2026-04-26 10:20:30.123456+00:00');
        $sameB = new DateTimeImmutable('2026-04-26 10:20:30.123456+00:00');
        $other = new DateTimeImmutable('2026-04-26 10:20:30.123457+00:00');

        self::assertTrue($comparator->equals($sameA, $sameB));
        self::assertFalse($comparator->equals($sameA, $other));
    }

    public function test_strict_comparator_treats_same_instant_with_different_timezones_as_equal(): void
    {
        $comparator = StrictComparator::create();

        $lima = new DateTimeImmutable('2026-04-30 00:00:00-05:00');
        $utc  = new DateTimeImmutable('2026-04-30 05:00:00+00:00');

        self::assertTrue($comparator->equals($lima, $utc));
    }

    public function test_numeric_comparator_compares_numeric_values_semantically(): void
    {
        $comparator = NumericComparator::create();

        self::assertTrue($comparator->equals(10, 10));
        self::assertTrue($comparator->equals(10, 10.0));
        self::assertTrue($comparator->equals('10', 10));
        self::assertTrue($comparator->equals(10, '10'));
        self::assertTrue($comparator->equals('10.5', 10.5));
        self::assertTrue($comparator->equals('010.000', '10'));
        self::assertTrue($comparator->equals('+0.5000', '.5'));
        self::assertTrue($comparator->equals('-0.5000', '-.5'));
        self::assertTrue($comparator->equals('-10.0', -10));
        self::assertTrue($comparator->equals('-0.0', 0));
        self::assertTrue($comparator->equals('1e3', 1000));
        self::assertTrue($comparator->equals('1E-5', '0.00001'));
        self::assertTrue($comparator->equals('+12.34e-2', '.1234'));
    }

    public function test_numeric_comparator_preserves_large_integer_precision(): void
    {
        $comparator = NumericComparator::create();

        self::assertFalse(
            $comparator->equals('9007199254740993', '9007199254740992'),
        );
    }

    public function test_numeric_comparator_accepts_native_floats_that_php_renders_in_scientific_notation(): void
    {
        $comparator = NumericComparator::create();

        self::assertTrue($comparator->equals(0.00001, 0.00001));
        self::assertTrue($comparator->equals(100000000000000.0, 100000000000000.0));
    }

    public function test_numeric_comparator_is_not_affected_by_php_precision_for_native_floats(): void
    {
        $comparator        = NumericComparator::create();
        $originalPrecision = ini_get('precision');

        try {
            ini_set('precision', '17');

            self::assertTrue($comparator->equals(0.1 + 0.2, 0.3));
        } finally {
            if ($originalPrecision !== false) {
                ini_set('precision', (string)$originalPrecision);
            }
        }
    }

    public function test_numeric_comparator_throws_when_values_are_not_numeric(): void
    {
        $comparator = NumericComparator::create();

        $this->expectException(ComparisonTypeMismatch::class);

        $comparator->equals('hola', 10);
    }

    public function test_numeric_comparator_rejects_malformed_scientific_notation(): void
    {
        $comparator = NumericComparator::create();

        $this->expectException(ComparisonTypeMismatch::class);

        $comparator->equals('1e', 1000);
    }

    public function test_numeric_comparator_handles_nulls_without_exception(): void
    {
        $comparator = NumericComparator::create();

        self::assertTrue($comparator->equals(null, null));
        self::assertFalse($comparator->equals(null, 10));
    }

    public function test_loose_comparator_uses_loose_equality(): void
    {
        $comparator = LooseComparator::create();

        self::assertTrue($comparator->equals('10', 10));
        self::assertTrue($comparator->equals(1, true));
        self::assertTrue($comparator->equals(0, false));
        self::assertTrue($comparator->equals('1', true));
        self::assertTrue($comparator->equals('0', false));
        self::assertTrue($comparator->equals('10.5', 10.5));
        self::assertTrue($comparator->equals(null, false));
        self::assertFalse($comparator->equals('10', 11));
        self::assertFalse($comparator->equals('hola', 10));
    }

    public function test_datetime_comparator_compares_only_datetime_values(): void
    {
        $comparator = DateTimeComparator::create();

        $value   = new DateTimeImmutable('2026-04-26 10:20:30');
        $current = new DateTimeImmutable('2026-04-26 10:20:30');
        $other   = new DateTimeImmutable('2026-04-27 10:20:30');

        self::assertTrue($comparator->equals($value, $current));
        self::assertFalse($comparator->equals($value, $other));
        self::assertTrue($comparator->equals('2026-04-26 10:20:30', $current));
        self::assertTrue($comparator->equals('2026-04-26 10:20:30', '2026-04-26 10:20:30'));
        self::assertTrue($comparator->equals(1714126830, '2024-04-26 10:20:30+00:00'));
    }

    public function test_datetime_comparator_treats_same_instant_with_different_timezones_as_equal(): void
    {
        $comparator = DateTimeComparator::create();

        $lima = new DateTimeImmutable('2026-04-30 00:00:00-05:00');
        $utc  = new DateTimeImmutable('2026-04-30 05:00:00+00:00');

        self::assertTrue($comparator->equals($lima, $utc));
    }

    public function test_datetime_comparator_throws_when_values_are_not_comparable_as_dates(): void
    {
        $comparator = DateTimeComparator::create();

        $this->expectException(ComparisonTypeMismatch::class);

        $comparator->equals('not-a-date', 10);
    }

    public function test_datetime_comparator_preserves_value_current_order_in_error_messages(): void
    {
        $comparator = DateTimeComparator::create();

        try {
            $comparator->equals('2026-01-01', true);
            self::fail('Expected ComparisonTypeMismatch to be thrown.');
        } catch (ComparisonTypeMismatch $exception) {
            self::assertSame(
                'DateTime comparison requires DateTimeInterface or parseable date strings. Got <string> and <bool>.',
                $exception->getMessage(),
            );
        }
    }

    public function test_datetime_comparator_rejects_float_timestamps(): void
    {
        $comparator = DateTimeComparator::create();

        $this->expectException(ComparisonTypeMismatch::class);

        $comparator->equals(1715126830.25, '2024-05-08 00:07:10.250000+00:00');
    }

    public function test_datetime_comparator_handles_nulls_without_exception(): void
    {
        $comparator = DateTimeComparator::create();

        self::assertTrue($comparator->equals(null, null));
        self::assertFalse($comparator->equals(null, '2026-04-26 10:20:30'));
    }

    public function test_array_comparator_compares_nested_arrays_with_strict_items(): void
    {
        $comparator = ArrayComparator::create();

        self::assertTrue($comparator->equals(
            ['name' => 'Juan', 'meta' => ['age' => 20]],
            ['name' => 'Juan', 'meta' => ['age' => 20]],
        ));
    }

    public function test_array_comparator_with_strict_items_throws_on_incompatible_types(): void
    {
        $comparator = ArrayComparator::create();

        $this->expectException(ComparisonTypeMismatch::class);

        $comparator->equals(
            ['name' => 'Juan', 'meta' => ['age' => 20]],
            ['name' => 'Juan', 'meta' => ['age' => '20']],
        );
    }

    public function test_array_comparator_accepts_custom_item_comparator(): void
    {
        $comparator = ArrayComparator::create(LooseComparator::create());

        self::assertTrue($comparator->equals(
            ['meta' => ['age' => '20', 'active' => 1]],
            ['meta' => ['age' => 20, 'active' => true]],
        ));
    }

    public function test_array_comparator_accepts_custom_item_closure(): void
    {
        $comparator = ArrayComparator::create(
            static fn(mixed $value, mixed $current): bool => strtolower((string)$value) === strtolower((string)$current),
        );

        self::assertTrue($comparator->equals(
            ['profile' => ['name' => 'JUAN']],
            ['profile' => ['name' => 'juan']],
        ));
    }

    public function test_array_comparator_throws_when_values_are_not_arrays(): void
    {
        $comparator = ArrayComparator::create();

        $this->expectException(ComparisonTypeMismatch::class);

        $comparator->equals(['a' => 1], 'not-array');
    }

    public function test_array_comparator_handles_nulls_without_exception(): void
    {
        $comparator = ArrayComparator::create();

        self::assertTrue($comparator->equals(null, null));
        self::assertFalse($comparator->equals(null, ['a' => 1]));
    }
}
