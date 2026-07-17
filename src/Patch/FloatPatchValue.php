<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Patch;

use InvalidArgumentException;

/**
 * @extends NormalizablePatchValue<float|null>
 */
final readonly class FloatPatchValue extends NormalizablePatchValue
{
    public function __construct(
        bool $present,
        int|float|string|null $value,
    )
    {
        parent::__construct($present, $value);
    }

    protected static function normalize(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            return (float)$value;
        }

        $normalized = filter_var($value, FILTER_VALIDATE_FLOAT);

        if ($normalized === false) {
            throw new InvalidArgumentException(sprintf('Invalid float patch value [%s].', $value));
        }

        return $normalized;
    }
}
