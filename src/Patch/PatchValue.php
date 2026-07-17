<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Patch;

/**
 * @template TValue
 */
interface PatchValue
{
    public function isPresent(): bool;

    /**
     * @return TValue
     */
    public function value(): mixed;
}
