<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Integration;

use DateTimeImmutable;
use Vaened\DeltaOrchestrator\Action;
use Vaened\DeltaOrchestrator\Comparison\DateTimeComparator;
use Vaened\DeltaOrchestrator\Comparison\LooseComparator;
use Vaened\DeltaOrchestrator\Comparison\NumericComparator;
use Vaened\DeltaOrchestrator\Field;
use Vaened\DeltaOrchestrator\Orchestrator;
use Vaened\DeltaOrchestrator\Patch\DateTimeImmutablePatchValue;
use Vaened\DeltaOrchestrator\Patch\StringPatchValue;
use Vaened\DeltaOrchestrator\Tests\Support\CreatesIntegrationScenarios;
use Vaened\DeltaOrchestrator\Tests\TestCase;

final class ComparatorIntegrationTest extends TestCase
{
    use CreatesIntegrationScenarios;

    public function test_numeric_comparator_skips_execution_when_numeric_values_match(): void
    {
        $field = $this->singleValueField(
            value  : new StringPatchValue(true, '10'),
            current: 10,
            compare: NumericComparator::create(),
        );

        $executed = false;

        $action = new Action(
            fields: [$field],
            when  : null,
            apply : static function (Field ...$fields) use (&$executed): void {
                $executed = true;
            },
        );

        (new Orchestrator())->register($action)->execute();

        self::assertFalse($executed);
    }

    public function test_numeric_comparator_executes_when_numeric_values_differ(): void
    {
        $field = $this->singleValueField(
            value  : new StringPatchValue(true, '11'),
            current: 10,
            compare: NumericComparator::create(),
        );

        $executed = false;

        $action = new Action(
            fields: [$field],
            when  : null,
            apply : static function (Field ...$fields) use (&$executed): void {
                $executed = true;
            },
        );

        (new Orchestrator())->register($action)->execute();

        self::assertTrue($executed);
    }

    public function test_loose_comparator_skips_execution_when_boolean_like_values_match(): void
    {
        $field = $this->singleValueField(
            value  : new StringPatchValue(true, '1'),
            current: true,
            compare: LooseComparator::create(),
        );

        $executed = false;

        $action = new Action(
            fields: [$field],
            when  : null,
            apply : static function (Field ...$fields) use (&$executed): void {
                $executed = true;
            },
        );

        (new Orchestrator())->register($action)->execute();

        self::assertFalse($executed);
    }

    public function test_datetime_comparator_skips_execution_when_dates_match(): void
    {
        $field = $this->singleValueField(
            value  : new DateTimeImmutablePatchValue(true, '2026-04-26 10:20:30'),
            current: new DateTimeImmutable('2026-04-26 10:20:30'),
            compare: DateTimeComparator::create(),
        );

        $executed = false;

        $action = new Action(
            fields: [$field],
            when  : null,
            apply : static function (Field ...$fields) use (&$executed): void {
                $executed = true;
            },
        );

        (new Orchestrator())->register($action)->execute();

        self::assertFalse($executed);
    }
}
