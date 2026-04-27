<?php

declare(strict_types=1);

final readonly class UserProfileApplicationService
{
    public function __construct(
        private ScenarioReporter $reporter,
    )
    {
    }

    public function rename(string $displayName): void
    {
        $this->reporter->executed("RenameUser: {$displayName}");
    }

    public function changeEmail(string $email): void
    {
        $this->reporter->executed("ChangeEmail: {$email}");
    }

    public function rebuildPersonalData(string $displayName, ?DateTimeImmutable $birthDate): void
    {
        $birthDateText = $birthDate?->format('Y-m-d') ?? 'null';

        $this->reporter->executed(
            "RebuildPersonalData: displayName={$displayName}, birthDate={$birthDateText}",
        );
    }

    public function synchronizeIdentityProvider(string $displayName, string $email): void
    {
        $this->reporter->executed(
            "SynchronizeIdentityProvider: displayName={$displayName}, email={$email}",
        );
    }

    public function updateMarketingConsent(bool $enabled): void
    {
        $this->reporter->executed(
            'UpdateMarketingConsent: ' . ($enabled ? 'enabled' : 'disabled'),
        );
    }

    public function updateTimezone(string $timezone): void
    {
        $this->reporter->executed("UpdateTimezone: {$timezone}");
    }

    public function rebuildSearchIndex(string $displayName, string $email, ?DateTimeImmutable $birthDate): void
    {
        $birthDateText = $birthDate?->format('Y-m-d') ?? 'null';

        $this->reporter->executed(
            "RebuildSearchIndex: displayName={$displayName}, email={$email}, birthDate={$birthDateText}",
        );
    }
}
