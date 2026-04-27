<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Patch;

use InvalidArgumentException;

/**
 * @implements PatchValue<bool|null>
 */
final readonly class BoolPatchValue implements PatchValue
{
    private bool $present;

    private ?bool $value;

    public function __construct(
        bool $present,
        bool|int|string|null $value,
    )
    {
        $this->present = $present;
        $this->value   = self::normalize($value);
    }

    public function isPresent(): bool
    {
        return $this->present;
    }

    public function value(): ?bool
    {
        return $this->value;
    }

    private static function normalize(bool|int|string|null $value): ?bool
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
