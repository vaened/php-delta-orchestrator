<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Patch;

use Vaened\DeltaOrchestrator\Exceptions\InvalidPatchValue;

/**
 * @extends NormalizablePatchValue<bool|null>
 */
final readonly class BoolPatchValue extends NormalizablePatchValue
{
    public function __construct(
        bool $present,
        bool|int|string|null $value,
    )
    {
        parent::__construct($present, $value);
    }

    protected static function normalize(mixed $value): ?bool
    {
        if ($value === null || is_bool($value)) {
            return $value;
        }

        $normalized = filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

        if ($normalized === null) {
            throw InvalidPatchValue::forBool($value);
        }

        return $normalized;
    }
}
