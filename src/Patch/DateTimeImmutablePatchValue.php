<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Patch;

use DateTimeImmutable;

/**
 * @implements PatchValue<DateTimeImmutable|null>
 */
final readonly class DateTimeImmutablePatchValue implements PatchValue
{
    public function __construct(
        private bool               $present,
        private ?DateTimeImmutable $value,
    )
    {
    }

    public function isPresent(): bool
    {
        return $this->present;
    }

    public function value(): ?DateTimeImmutable
    {
        return $this->value;
    }
}
