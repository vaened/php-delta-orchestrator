<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Patch;

use Stringable;

/**
 * @extends NormalizablePatchValue<string|null>
 */
final readonly class StringPatchValue extends NormalizablePatchValue
{
    public function __construct(
        bool $present,
        string|int|float|Stringable|null $value,
    )
    {
        parent::__construct($present, $value);
    }

    protected static function normalize(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return (string)$value;
    }
}
