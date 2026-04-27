<?php

declare(strict_types=1);

final readonly class UserProfile
{
    public function __construct(
        public string             $displayName,
        public string             $email,
        public ?DateTimeImmutable $birthDate,
        public bool               $marketingConsent,
        public string             $timezone,
    ) {
    }
}
