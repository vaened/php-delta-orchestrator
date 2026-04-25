<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Bindings;

use Vaened\DeltaOrchestrator\Field;

interface Behavior
{
    public function field(): Field;

    public function satisfies(): bool;
}
