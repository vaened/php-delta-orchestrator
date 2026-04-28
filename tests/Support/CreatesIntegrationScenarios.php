<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Support;

use Closure;
use DateTimeImmutable;
use DateTimeInterface;
use Vaened\DeltaOrchestrator\Comparison\Comparator;
use Vaened\DeltaOrchestrator\Field;
use Vaened\DeltaOrchestrator\Patch\DateTimeImmutablePatchValue;
use Vaened\DeltaOrchestrator\Patch\IntPatchValue;
use Vaened\DeltaOrchestrator\Patch\PatchValue;
use Vaened\DeltaOrchestrator\Patch\StringPatchValue;
use Vaened\DeltaOrchestrator\Tests\Support\Fixtures\ProfileState;
use Vaened\DeltaOrchestrator\Tests\Support\Fixtures\UpdateProfileCommand;

trait CreatesIntegrationScenarios
{
    protected function updateProfileCommand(
        bool                           $namePresent = true,
        string|int|float|null          $name = 'Juan',
        bool                           $agePresent = true,
        int|string|null                $age = '20',
        bool                           $birthdayPresent = true,
        DateTimeInterface|string|null $birthday = '2026-04-26 10:20:30',
    ): UpdateProfileCommand
    {
        return new UpdateProfileCommand(
            name    : new StringPatchValue($namePresent, $name),
            age     : new IntPatchValue($agePresent, $age),
            birthday: new DateTimeImmutablePatchValue($birthdayPresent, $birthday),
        );
    }

    protected function profileState(
        string             $name = 'Pedro',
        int                $age = 18,
        ?DateTimeImmutable $birthday = null,
    ): ProfileState
    {
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
        ?UpdateProfileCommand   $payload = null,
        ?ProfileState           $current = null,
        Comparator|Closure|null $ageComparator = null,
        Comparator|Closure|null $birthdayComparator = null,
    ): array
    {
        $payload ??= $this->updateProfileCommand();
        $current ??= $this->profileState();

        return [
            'name'     => Field::from(
                patch  : $payload->name,
                current: $current->name,
            ),
            'age'      => Field::from(
                patch     : $payload->age,
                current   : $current->age,
                comparator: $ageComparator,
            ),
            'birthday' => Field::from(
                patch     : $payload->birthday,
                current   : $current->birthday,
                comparator: $birthdayComparator,
            ),
        ];
    }

    protected function singleValueField(
        PatchValue              $value,
        mixed                   $current,
        Comparator|Closure|null $compare = null,
    ): Field
    {
        return Field::from(
            patch     : $value,
            current   : $current,
            comparator: $compare,
        );
    }
}
