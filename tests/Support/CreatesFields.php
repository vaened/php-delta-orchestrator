<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Support;

use Closure;
use Vaened\DeltaOrchestrator\Comparison\Comparator;
use Vaened\DeltaOrchestrator\Field;

trait CreatesFields
{
    protected function field(
        mixed                   $value,
        mixed                   $current = null,
        bool                    $present = true,
        Comparator|Closure|null $comparator = null,
    ): Field
    {
        return new Field(
            patch     : fn(): FakePatchValue => new FakePatchValue($present, $value),
            current   : fn(): mixed => $current,
            comparator: $comparator,
        );
    }
}
