<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Patch;

use InvalidArgumentException;

/**
 * @implements PatchValue<float|null>
 */
final readonly class FloatPatchValue implements PatchValue
{
    private bool $present;

    private ?float $value;

    public function __construct(
        bool $present,
        int|float|string|null $value,
    )
    {
        $this->present = $present;
        $this->value   = self::normalize($value);
    }

    public function isPresent(): bool
    {
        return $this->present;
    }

    public function value(): ?float
    {
        return $this->value;
    }

    private static function normalize(int|float|string|null $value): ?float
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
