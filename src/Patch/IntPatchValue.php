<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Patch;

/**
 * @implements PatchValue<int|null>
 */
final readonly class IntPatchValue implements PatchValue
{
    public function __construct(
        private bool $present,
        private ?int $value,
    )
    {
    }

    public function isPresent(): bool
    {
        return $this->present;
    }

    public function value(): ?int
    {
        return $this->value;
    }
}
