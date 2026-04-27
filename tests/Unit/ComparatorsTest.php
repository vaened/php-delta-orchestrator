<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Unit;

use DateTimeImmutable;
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

    public function test_strict_comparator_compares_datetimes_by_exact_temporal_value(): void
    {
        $comparator = StrictComparator::create();

        $sameA = new DateTimeImmutable('2026-04-26 10:20:30.123456+00:00');
        $sameB = new DateTimeImmutable('2026-04-26 10:20:30.123456+00:00');
        $other = new DateTimeImmutable('2026-04-26 10:20:30.123457+00:00');

        self::assertTrue($comparator->equals($sameA, $sameB));
        self::assertFalse($comparator->equals($sameA, $other));
    }

    public function test_numeric_comparator_compares_numeric_values_semantically(): void
    {
        $comparator = NumericComparator::create();

        self::assertTrue($comparator->equals(10, 10));
        self::assertTrue($comparator->equals(10, 10.0));
        self::assertTrue($comparator->equals('10', 10));
        self::assertTrue($comparator->equals(10, '10'));
        self::assertTrue($comparator->equals('10.5', 10.5));
        self::assertFalse($comparator->equals('hola', 10));
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
    }

    public function test_datetime_comparator_throws_when_values_are_not_comparable_as_dates(): void
    {
        $comparator = DateTimeComparator::create();

        $this->expectException(ComparisonTypeMismatch::class);

        $comparator->equals('not-a-date', 10);
    }
}
