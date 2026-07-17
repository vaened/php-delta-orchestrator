<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Patch;

use InvalidArgumentException;

/**
 * @extends PatchValue<bool|null>
 */
final readonly class BoolPatchValue extends PatchValue
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
            throw new InvalidArgumentException(sprintf('Invalid bool patch value [%s].', (string)$value));
        }

        return $normalized;
    }
}
