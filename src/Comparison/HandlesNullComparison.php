<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Comparison;

trait HandlesNullComparison
{
    protected function compareNulls(mixed $value, mixed $current): ?bool
    {
        if ($value === null && $current === null) {
            return true;
        }

        if ($value === null || $current === null) {
            return false;
        }

        return null;
    }
}
