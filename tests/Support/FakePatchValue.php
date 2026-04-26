<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Support;

use Vaened\DeltaOrchestrator\Patch\PatchValue;

/**
 * @template TValue
 *
 * @implements PatchValue<TValue>
 */
final readonly class FakePatchValue implements PatchValue
{
    /**
     * @param TValue $value
     */
    public function __construct(
        private bool $present,
        private mixed $value,
    )
    {
    }

    public function isPresent(): bool
    {
        return $this->present;
    }

    /**
     * @return TValue
     */
    public function value(): mixed
    {
        return $this->value;
    }
}
