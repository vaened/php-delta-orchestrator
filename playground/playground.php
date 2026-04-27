<?php

declare(strict_types=1);

use Vaened\DeltaOrchestrator\Action;
use Vaened\DeltaOrchestrator\Comparison\DateTimeComparator;
use Vaened\DeltaOrchestrator\Field;
use Vaened\DeltaOrchestrator\Orchestrator;
use Vaened\DeltaOrchestrator\Patch\PatchInput;
use Vaened\DeltaOrchestrator\Schema;

use function Vaened\DeltaOrchestrator\Rules\all;
use function Vaened\DeltaOrchestrator\Rules\any;
use function Vaened\DeltaOrchestrator\Rules\present;

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/Support/UserProfile.php';
require __DIR__ . '/Support/ScenarioReporter.php';
require __DIR__ . '/Support/UserProfileApplicationService.php';

$currentUser = new UserProfile(
    displayName     : 'Pedro',
    email           : 'pedro@old.test',
    birthDate       : new DateTimeImmutable('1990-10-20'),
    marketingConsent: false,
    timezone        : 'America/Lima',
);

$payload = new PatchInput(
    input       : [
        // Present but equal: activates presence-based actions, but produces no delta.
        'displayName'      => 'Pedro',

        // Present and different: produces an effective delta.
        'email'            => 'pedro@new.test',

        // Present with semantic DateTime comparison.
        'birthDate'        => '1990-10-20 00:00:00',

        // Present with null: useful to demonstrate a contract failure in a controlled action.
        'marketingConsent' => null,
    ],
    expectedKeys: [
        'displayName',
        'email',
        'birthDate',
        'marketingConsent',
    ],
);

$schema = new Schema(
    payload: $payload,
    current: $currentUser,
);

$displayName = $schema->define(
    patch  : fn(PatchInput $payload) => $payload->string('displayName'),
    current: fn(UserProfile $current) => $current->displayName,
);

$email = $schema->define(
    patch  : fn(PatchInput $payload) => $payload->string('email'),
    current: fn(UserProfile $current) => $current->email,
);

$birthDate = $schema->define(
    patch  : fn(PatchInput $payload) => $payload->dateTimeImmutable('birthDate'),
    current: fn(UserProfile $current) => $current->birthDate,
    compare: DateTimeComparator::create(),
);

$marketingConsent = $schema->define(
    patch  : fn(PatchInput $payload) => $payload->bool('marketingConsent'),
    current: fn(UserProfile $current) => $current->marketingConsent,
);

$timezone = $schema->define(
    patch  : fn(PatchInput $payload) => $payload->string('timezone'),
    current: fn(UserProfile $current) => $current->timezone,
);

$reporter     = new ScenarioReporter();
$service      = new UserProfileApplicationService($reporter);
$orchestrator = new Orchestrator();

$orchestrator->register(new Action(
    fields     : [$displayName->required()],
    apply      : function (Field $displayName) use ($service): void {
        $service->rename($displayName->value());
    },
    description: 'Skipped because displayName is present but unchanged',
));

$orchestrator->register(new Action(
    fields     : [$email->required()],
    apply      : function (Field $email) use ($service): void {
        $service->changeEmail($email->value());
    },
    description: 'Runs because email changed',
));

$orchestrator->register(new Action(
    fields     : [
        $displayName->optional(),
        $birthDate->optional(),
    ],
    apply      : function (Field $displayName, Field $birthDate) use ($service): void {
        $nextDisplayName = $displayName->isPresent()
            ? $displayName->value()
            : $displayName->current();

        $nextBirthDate = $birthDate->isPresent()
            ? $birthDate->value()
            : $birthDate->current();

        $service->rebuildPersonalData(
            displayName: $nextDisplayName,
            birthDate  : $nextBirthDate,
        );
    },
    when       : static fn(Field $displayName, Field $birthDate) => any([
        present($displayName),
        present($birthDate),
    ]),
    description: 'Runs only if one of the personal-data fields is present and changed',
));

$orchestrator->register(new Action(
    fields     : [
        $displayName->optional(),
        $email->required(),
    ],
    apply      : function (Field $displayName, Field $email) use ($service): void {
        $nextDisplayName = $displayName->isPresent()
            ? $displayName->value()
            : $displayName->current();

        $service->synchronizeIdentityProvider(
            displayName: $nextDisplayName,
            email      : $email->value(),
        );
    },
    when       : static fn(Field $displayName, Field $email) => any([
        present($displayName),
        present($email),
    ]),
    description: 'Runs because email changed and displayName can fallback to current value',
));

$orchestrator->register(new Action(
    fields     : [
        $email->required(),
        $birthDate->optional(),
        $displayName->optional(),
    ],
    apply      : function (Field $email, Field $birthDate, Field $displayName) use ($service): void {
        $nextDisplayName = $displayName->isPresent()
            ? $displayName->value()
            : $displayName->current();

        $nextBirthDate = $birthDate->isPresent()
            ? $birthDate->value()
            : $birthDate->current();

        $service->rebuildSearchIndex(
            displayName: $nextDisplayName,
            email      : $email->value(),
            birthDate  : $nextBirthDate,
        );
    },
    when       : static fn(Field $email, Field $birthDate, Field $displayName) => any([
        present($email),
        present($birthDate),
        present($displayName),
    ]),
    description: 'Runs because one searchable field changed',
));

$orchestrator->register(new Action(
    fields     : [$timezone->required()],
    apply      : function (Field $timezone) use ($service): void {
        $service->updateTimezone($timezone->value());
    },
    description: 'Skipped because timezone is absent',
));

$orchestrator->register(new Action(
    fields     : [$marketingConsent->required()],
    apply      : function (Field $marketingConsent) use ($service): void {
        $service->updateMarketingConsent($marketingConsent->value());
    },
    when       : static fn(Field $marketingConsent) => all([$marketingConsent]),
    description: 'Fails because marketingConsent is present but null',
));

try {
    $orchestrator->execute();
} catch (Throwable $exception) {
    $reporter->failure('ContractFailure: ' . $exception::class);
}

$reporter->dump();
