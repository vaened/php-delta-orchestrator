<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Support;

use Closure;
use DateTimeImmutable;
use Vaened\DeltaOrchestrator\Comparison\Comparator;
use Vaened\DeltaOrchestrator\Field;
use Vaened\DeltaOrchestrator\Patch\DateTimeImmutablePatchValue;
use Vaened\DeltaOrchestrator\Patch\IntPatchValue;
use Vaened\DeltaOrchestrator\Patch\PatchValue;
use Vaened\DeltaOrchestrator\Patch\StringPatchValue;
use Vaened\DeltaOrchestrator\Schema;
use Vaened\DeltaOrchestrator\Tests\Support\Fixtures\ProfileState;
use Vaened\DeltaOrchestrator\Tests\Support\Fixtures\UpdateProfileCommand;

trait CreatesIntegrationScenarios
{
    protected function updateProfileCommand(
        bool $namePresent = true,
        string|int|float|null $name = 'Juan',
        bool $agePresent = true,
        int|string|null $age = '20',
        bool $birthdayPresent = true,
        \DateTimeInterface|string|null $birthday = '2026-04-26 10:20:30',
    ): UpdateProfileCommand {
        return new UpdateProfileCommand(
            name    : new StringPatchValue($namePresent, $name),
            age     : new IntPatchValue($agePresent, $age),
            birthday: new DateTimeImmutablePatchValue($birthdayPresent, $birthday),
        );
    }

    protected function profileState(
        string $name = 'Pedro',
        int $age = 18,
        ?DateTimeImmutable $birthday = null,
    ): ProfileState {
        return new ProfileState(
            name    : $name,
            age     : $age,
            birthday: $birthday ?? new DateTimeImmutable('1990-10-20 00:00:00'),
        );
    }

    /**
     * @return array{name: Field, age: Field, birthday: Field}
     */
    protected function profileFields(
        ?UpdateProfileCommand $payload = null,
        ?ProfileState $current = null,
        Comparator|Closure|null $ageComparator = null,
        Comparator|Closure|null $birthdayComparator = null,
    ): array {
        $schema = new Schema(
            payload: $payload ?? $this->updateProfileCommand(),
            current: $current ?? $this->profileState(),
        );

        return [
            'name' => $schema->define(
                value  : fn(UpdateProfileCommand $payload) => $payload->name,
                current: fn(ProfileState $current) => $current->name,
            ),
            'age' => $schema->define(
                value  : fn(UpdateProfileCommand $payload) => $payload->age,
                current: fn(ProfileState $current) => $current->age,
                compare: $ageComparator,
            ),
            'birthday' => $schema->define(
                value  : fn(UpdateProfileCommand $payload) => $payload->birthday,
                current: fn(ProfileState $current) => $current->birthday,
                compare: $birthdayComparator,
            ),
        ];
    }

    protected function singleValueField(
        PatchValue $value,
        mixed $current,
        Comparator|Closure|null $compare = null,
    ): Field {
        $payload = new class($value) {
            public function __construct(public PatchValue $value)
            {
            }
        };

        $state = new class($current) {
            public function __construct(public mixed $value)
            {
            }
        };

        $schema = new Schema(payload: $payload, current: $state);

        return $schema->define(
            value  : fn(object $payload) => $payload->value,
            current: fn(object $current) => $current->value,
            compare: $compare,
        );
    }
}
