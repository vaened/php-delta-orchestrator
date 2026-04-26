<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Unit;

use Vaened\DeltaOrchestrator\Comparison\Comparator;
use Vaened\DeltaOrchestrator\Tests\TestCase;

use function strtolower;

final class FieldTest extends TestCase
{
    public function test_it_resolves_presence_value_and_current(): void
    {
        $field = $this->field(value: 'Juan', current: 'Pedro', present: true);

        self::assertTrue($field->isPresent());
        self::assertSame('Juan', $field->value());
        self::assertSame('Pedro', $field->current());
    }

    public function test_it_is_not_changed_when_patch_value_is_absent(): void
    {
        $field = $this->field(value: 'Juan', current: 'Pedro', present: false);

        self::assertFalse($field->changed());
        self::assertNull($field->delta());
    }

    public function test_it_returns_null_delta_when_values_match(): void
    {
        $field = $this->field(value: 'Pedro', current: 'Pedro');

        self::assertTrue($field->matches());
        self::assertFalse($field->changed());
        self::assertNull($field->delta());
    }

    public function test_it_builds_delta_when_value_changes(): void
    {
        $field = $this->field(value: 'Juan', current: 'Pedro');

        self::assertTrue($field->changed());
        self::assertFalse($field->matches());
        self::assertSame('Pedro', $field->delta()?->previous());
        self::assertSame('Juan', $field->delta()?->next());
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
        self::assertFalse($field->changed());
        self::assertNull($field->delta());
    }
}
