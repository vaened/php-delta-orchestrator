<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Unit;

use Vaened\DeltaOrchestrator\Rules\All;
use Vaened\DeltaOrchestrator\Rules\Any;
use Vaened\DeltaOrchestrator\Rules\Boolean;
use Vaened\DeltaOrchestrator\Rules\Present;
use Vaened\DeltaOrchestrator\Tests\TestCase;

use function Vaened\DeltaOrchestrator\Rules\all;
use function Vaened\DeltaOrchestrator\Rules\any;

final class RulesTest extends TestCase
{
    public function test_present_returns_true_when_field_is_present(): void
    {
        self::assertTrue((new Present($this->field(value: 'Juan', present: true)))->satisfies());
    }

    public function test_present_returns_false_when_field_is_absent(): void
    {
        self::assertFalse((new Present($this->field(value: 'Juan', present: false)))->satisfies());
    }

    public function test_all_requires_every_rule_to_satisfy(): void
    {
        $rule = new All([
            $this->field(value: 'Juan', present: true),
            $this->field(value: 'Pedro', present: true),
        ]);

        self::assertTrue($rule->satisfies());
    }

    public function test_all_returns_false_when_one_rule_fails(): void
    {
        $rule = all([
            $this->field(value: 'Juan', present: true),
            $this->field(value: 'Pedro', present: false),
        ]);

        self::assertFalse($rule->satisfies());
    }

    public function test_any_returns_true_when_one_rule_satisfies(): void
    {
        $rule = any([
            $this->field(value: 'Juan', present: false),
            $this->field(value: 'Pedro', present: true),
        ]);

        self::assertTrue($rule->satisfies());
    }

    public function test_any_returns_false_when_all_rules_fail(): void
    {
        $rule = new Any([
            $this->field(value: 'Juan', present: false),
            $this->field(value: 'Pedro', present: false),
        ]);

        self::assertFalse($rule->satisfies());
    }

    public function test_any_is_present_named_constructor_builds_when_closure(): void
    {
        $when = Any::isPresent();

        self::assertTrue(
            $when(
                $this->field(value: 'Juan', present: false),
                $this->field(value: 'Pedro', present: true),
            )->satisfies(),
        );
    }

    public function test_all_is_present_named_constructor_builds_when_closure(): void
    {
        $when = All::isPresent();

        self::assertFalse(
            $when(
                $this->field(value: 'Juan', present: true),
                $this->field(value: 'Pedro', present: false),
            )->satisfies(),
        );
    }

    public function test_boolean_from_wraps_fixed_value_as_rule(): void
    {
        self::assertTrue(Boolean::from(true)->satisfies());
        self::assertFalse(Boolean::from(false)->satisfies());
    }

    public function test_boolean_resolve_builds_when_closure(): void
    {
        $when = Boolean::resolve(
            static fn($startDate, $endDate): bool => $startDate->isPresent() && $endDate->isPresent(),
        );

        self::assertFalse(
            $when(
                $this->field(value: 'Juan', present: true),
                $this->field(value: 'Pedro', present: false),
            )->satisfies(),
        );
    }
}
