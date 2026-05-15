<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Unit;

use DateTimeImmutable;
use Vaened\DeltaOrchestrator\Field;
use Vaened\DeltaOrchestrator\Exceptions\ComparisonTypeMismatch;
use Vaened\DeltaOrchestrator\Comparison\Comparator;
use Vaened\DeltaOrchestrator\Tests\TestCase;

use function strtolower;

final class FieldTest extends TestCase
{
    public function test_it_reports_present_when_patch_value_is_present(): void
    {
        $field = $this->field(value: 'Juan', current: 'Pedro', present: true);

        self::assertTrue($field->isPresent());
        self::assertFalse($field->isAbsent());
    }

    public function test_it_reports_absent_when_patch_value_is_absent(): void
    {
        $field = $this->field(value: 'Juan', current: 'Pedro', present: false);

        self::assertFalse($field->isPresent());
        self::assertTrue($field->isAbsent());
    }

    public function test_it_resolves_value_and_current(): void
    {
        $field = $this->field(value: 'Juan', current: 'Pedro', present: true);

        self::assertSame('Juan', $field->value());
        self::assertSame('Pedro', $field->current());
    }

    public function test_it_is_not_changed_when_patch_value_is_absent(): void
    {
        $field = $this->field(value: 'Juan', current: 'Pedro', present: false);

        self::assertFalse($field->isChanged());
        self::assertNull($field->delta());
    }

    public function test_it_returns_null_delta_when_values_match(): void
    {
        $field = $this->field(value: 'Pedro', current: 'Pedro');

        self::assertTrue($field->matches());
        self::assertFalse($field->isChanged());
        self::assertNull($field->delta());
    }

    public function test_it_uses_strict_comparator_by_default(): void
    {
        $field = $this->field(value: 10, current: 10.0);

        $this->expectException(ComparisonTypeMismatch::class);

        $field->matches();
    }

    public function test_it_uses_default_strict_datetime_comparison(): void
    {
        $field = $this->field(
            value  : new DateTimeImmutable('2026-04-26 10:20:30.123456+00:00'),
            current: new DateTimeImmutable('2026-04-26 10:20:30.123456+00:00'),
        );

        self::assertTrue($field->matches());
        self::assertFalse($field->isChanged());
    }

    public function test_it_builds_delta_when_value_changes(): void
    {
        $field = $this->field(value: 'Juan', current: 'Pedro');

        self::assertTrue($field->isChanged());
        self::assertSame('Juan', $field->changed());
        self::assertFalse($field->matches());
        self::assertSame('Pedro', $field->delta()?->previous());
        self::assertSame('Juan', $field->delta()?->next());
    }

    public function test_it_returns_null_changed_value_when_values_do_not_change(): void
    {
        $field = $this->field(value: 'Pedro', current: 'Pedro');

        self::assertNull($field->changed());
    }

    public function test_it_uses_custom_comparator(): void
    {
        $comparator = new class implements Comparator {
            public function equals(mixed $value, mixed $current): bool
            {
                return strtolower((string)$value) === strtolower((string)$current);
            }
        };

        $field = $this->field(
            value     : 'PEDRO',
            current   : 'pedro',
            comparator: $comparator,
        );

        self::assertTrue($field->matches());
        self::assertFalse($field->isChanged());
        self::assertNull($field->delta());
    }

    public function test_it_uses_using_fluent_method(): void
    {
        $field = $this->field(value: 'PEDRO', current: 'pedro')
            ->using(new class implements Comparator {
                public function equals(mixed $value, mixed $current): bool
                {
                    return strtolower((string)$value) === strtolower((string)$current);
                }
            });

        self::assertTrue($field->matches());
        self::assertFalse($field->isChanged());
    }

    public function test_it_transforms_value_before_comparison_and_delta(): void
    {
        $field = $this->field(value: '  PEDRO  ', current: 'pedro')
            ->transform(static fn(string $value): string => strtolower(trim($value)));

        self::assertSame('pedro', $field->value());
        self::assertTrue($field->matches());
        self::assertFalse($field->isChanged());
        self::assertNull($field->delta());
    }

    public function test_it_recomputes_matches_when_using_is_set_after_transform(): void
    {
        $field = $this->field(value: '  PEDRO  ', current: 'pedro')
            ->transform(static fn(string $value): string => trim($value))
            ->using(new class implements Comparator {
                public function equals(mixed $value, mixed $current): bool
                {
                    return strtolower((string)$value) === strtolower((string)$current);
                }
            });

        self::assertTrue($field->matches());
        self::assertFalse($field->isChanged());
        self::assertSame('PEDRO', $field->value());
    }

    public function test_it_returns_effective_patch_value_when_present(): void
    {
        $field = $this->field(value: 'Juan', current: 'Pedro', present: true);

        self::assertSame('Juan', $field->effective());
    }

    public function test_it_returns_effective_current_value_when_absent(): void
    {
        $field = $this->field(value: 'Juan', current: 'Pedro', present: false);

        self::assertSame('Pedro', $field->effective());
    }

    public function test_not_nullable_transformer_keeps_null_and_transforms_non_null(): void
    {
        $nullField = $this->field(value: null, current: null)
            ->transform(Field::notNullable(
                static fn(string $value): string => strtolower($value),
            ));

        self::assertNull($nullField->value());

        $valueField = $this->field(value: 'PEDRO', current: 'pedro')
            ->transform(Field::notNullable(
                static fn(string $value): string => strtolower($value),
            ));

        self::assertSame('pedro', $valueField->value());
        self::assertTrue($valueField->matches());
    }
}
