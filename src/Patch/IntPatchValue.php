<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Patch;

use InvalidArgumentException;

/**
 * @implements PatchValue<int|null>
 */
final readonly class IntPatchValue implements PatchValue
{
    private bool $present;

    private ?int $value;

    public function __construct(
        bool $present,
        int|string|null $value,
    )
    {
        $this->present = $present;
        $this->value   = self::normalize($value);
    }

    public function isPresent(): bool
    {
        return $this->present;
    }

    public function value(): ?int
    {
        return $this->value;
    }

    private static function normalize(int|string|null $value): ?int
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
