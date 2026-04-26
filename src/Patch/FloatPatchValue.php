<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Patch;

/**
 * @implements PatchValue<float|null>
 */
final readonly class FloatPatchValue implements PatchValue
{
    public function __construct(
        private bool $present,
        private ?float $value,
    )
    {
    }

    public function isPresent(): bool
    {
        return $this->present;
    }

    public function value(): ?float
    {
        return $this->value;
    }
}
