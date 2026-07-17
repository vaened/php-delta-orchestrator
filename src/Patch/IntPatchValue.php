<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Patch;

use InvalidArgumentException;

/**
 * @extends NormalizablePatchValue<int|null>
 */
final readonly class IntPatchValue extends NormalizablePatchValue
{
    public function __construct(
        bool $present,
        int|string|null $value,
    )
    {
        parent::__construct($present, $value);
    }

    protected static function normalize(mixed $value): ?int
    {
        if ($value === null || is_int($value)) {
            return $value;
        }

        $normalized = filter_var(
            $value,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => PHP_INT_MIN, 'max_range' => PHP_INT_MAX]],
        );

        if ($normalized === false) {
            throw new InvalidArgumentException(sprintf('Invalid int patch value [%s].', $value));
        }

        return $normalized;
    }
}
