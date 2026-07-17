<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Patch;

/**
 * @template TValue
 *
 * @implements PatchValue<TValue>
 */
abstract readonly class NormalizablePatchValue implements PatchValue
{
    /**
     * @var TValue
     */
    private mixed $value;

    /**
     * @param TValue $value
     */
    public function __construct(
        private bool $present,
        mixed        $value,
    )
    {
        $this->value = static::normalize($value);
    }

    abstract protected static function normalize(mixed $value): mixed;

    /**
     * @param TValue $value
     */
    public static function from(bool $present, mixed $value): static
    {
        return new static($present, $value);
    }

    /**
     * @param TValue $value
     */
    public static function present(mixed $value): static
    {
        return static::from(true, $value);
    }

    public static function missing(): static
    {
        return static::from(false, null);
    }

    public function isPresent(): bool
    {
        return $this->present;
    }

    /**
     * @return TValue
     */
    public function value(): mixed
    {
        return $this->value;
    }
}
