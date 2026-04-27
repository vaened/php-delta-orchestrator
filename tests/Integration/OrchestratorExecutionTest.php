<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Integration;

use DateTimeImmutable;
use Vaened\DeltaOrchestrator\Action;
use Vaened\DeltaOrchestrator\Comparison\NumericComparator;
use Vaened\DeltaOrchestrator\Field;
use Vaened\DeltaOrchestrator\Orchestrator;
use Vaened\DeltaOrchestrator\Patch\DateTimeImmutablePatchValue;
use Vaened\DeltaOrchestrator\Patch\StringPatchValue;
use Vaened\DeltaOrchestrator\Tests\Support\CreatesIntegrationScenarios;
use Vaened\DeltaOrchestrator\Tests\TestCase;

use function Vaened\DeltaOrchestrator\Rules\all;
use function Vaened\DeltaOrchestrator\Rules\any;

final class OrchestratorExecutionTest extends TestCase
{
    use CreatesIntegrationScenarios;

    public function test_it_executes_a_real_action_with_schema_and_patch_values(): void
    {
        ['name' => $name, 'age' => $age, 'birthday' => $birthday] = $this->profileFields(
            ageComparator: NumericComparator::create(),
        );

        $result = null;

        $action = new Action(
            fields      : [$name->required(), $age->required(), $birthday->optional()],
            when        : static fn(Field ...$fields) => all($fields),
            apply       : static function (Field ...$fields) use (&$result): void {
                [$name, $age, $birthday] = $fields;

                $result = [
                    'name' => $name->delta()?->next(),
                    'age' => $age->delta()?->next(),
                    'birthday' => $birthday->delta()?->next()?->format('Y-m-d H:i:s'),
                ];
            },
            description : 'Update profile',
        );

        (new Orchestrator())->register($action)->execute();

        self::assertSame(
            [
                'name' => 'Juan',
                'age' => 20,
                'birthday' => '2026-04-26 10:20:30',
            ],
            $result,
        );
    }

    public function test_it_skips_action_when_real_datetime_delta_does_not_exist(): void
    {
        $birthday = $this->singleValueField(
            value  : new DateTimeImmutablePatchValue(true, '2026-04-26 10:20:30'),
            current: new DateTimeImmutable('2026-04-26 10:20:30'),
        );

        $executed = false;

        $action = new Action(
            fields: [$birthday],
            when  : null,
            apply : static function (Field ...$fields) use (&$executed): void {
                $executed = true;
            },
        );

        (new Orchestrator())->register($action)->execute();

        self::assertFalse($executed);
    }

    public function test_it_skips_action_when_default_any_presence_does_not_activate(): void
    {
        ['name' => $name] = $this->profileFields(
            payload: $this->updateProfileCommand(
                namePresent    : false,
                name           : null,
                agePresent     : false,
                age            : null,
                birthdayPresent: false,
                birthday       : null,
            ),
        );

        $executed = false;

        $action = new Action(
            fields: [$name],
            when  : null,
            apply : static function (Field ...$fields) use (&$executed): void {
                $executed = true;
            },
        );

        (new Orchestrator())->register($action)->execute();

        self::assertFalse($executed);
    }

    public function test_it_executes_only_the_actions_that_apply(): void
    {
        $first  = $this->singleValueField(new StringPatchValue(false, null), 'Pedro');
        $second = $this->singleValueField(new StringPatchValue(true, 'Juan'), 'Pedro');

        $executed = [];

        $orchestrator = (new Orchestrator())
            ->register(new Action(
                fields      : [$first],
                when        : null,
                apply       : static function (Field ...$fields) use (&$executed): void {
                    $executed[] = 'first';
                },
                description : 'First action',
            ))
            ->register(new Action(
                fields      : [$second],
                when        : null,
                apply       : static function (Field ...$fields) use (&$executed): void {
                    $executed[] = 'second';
                },
                description : 'Second action',
            ));

        $orchestrator->execute();

        self::assertSame(['second'], $executed);
    }

    public function test_it_honors_a_custom_when_rule(): void
    {
        ['name' => $name, 'age' => $age] = $this->profileFields(
            payload: $this->updateProfileCommand(
                namePresent: false,
                name       : null,
                agePresent : true,
                age        : '20',
            ),
            ageComparator: NumericComparator::create(),
        );

        $executed = false;

        $action = new Action(
            fields: [$name->optional(), $age->required()],
            when  : static function (Field ...$fields) {
                [$name, $age] = $fields;

                return any([$name, $age]);
            },
            apply : static function (Field ...$fields) use (&$executed): void {
                $executed = true;
            },
        );

        (new Orchestrator())->register($action)->execute();

        self::assertTrue($executed);
    }
}
