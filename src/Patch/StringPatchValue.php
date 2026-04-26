<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Patch;

/**
 * @implements PatchValue<string|null>
 */
final readonly class StringPatchValue implements PatchValue
{
    public function __construct(
        private bool $present,
        private ?string $value,
    )
    {
    }

    public function isPresent(): bool
    {
        return $this->present;
    }

    public function value(): ?string
    {
        return $this->value;
    }
}
