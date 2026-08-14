<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Patch;

use InvalidArgumentException;

/**
 * @template TInput of array<string, mixed>
 */
final readonly class PatchInput
{
    /**
     * @param TInput $input
     */
    public function __construct(
        private array $input,
    )
    {
    }

    /**
     * @template TKey of key-of<TInput>
     * @param TKey $key
     */
    public function string(string $key): StringPatchValue
    {
        return new StringPatchValue(
            present: $this->isPresent($key),
            value  : $this->value($key),
        );
    }

    /**
     * @template TKey of key-of<TInput>
     * @param TKey $key
     */
    public function int(string $key): IntPatchValue
    {
        return new IntPatchValue(
            present: $this->isPresent($key),
            value  : $this->value($key),
        );
    }

    /**
     * @template TKey of key-of<TInput>
     * @param TKey $key
     */
    public function float(string $key): FloatPatchValue
    {
        return new FloatPatchValue(
            present: $this->isPresent($key),
            value  : $this->value($key),
        );
    }

    /**
     * @template TKey of key-of<TInput>
     * @param TKey $key
     */
    public function bool(string $key): BoolPatchValue
    {
        return new BoolPatchValue(
            present: $this->isPresent($key),
            value  : $this->value($key),
        );
    }

    /**
     * @template TKey of key-of<TInput>
     * @param TKey $key
     */
    public function dateTimeImmutable(string $key): DateTimeImmutablePatchValue
    {
        return new DateTimeImmutablePatchValue(
            present: $this->isPresent($key),
            value  : $this->value($key),
        );
    }

    /**
     * @template TKey of key-of<TInput>
     * @param TKey $key
     */
    public function array(string $key): ArrayPatchValue
    {
        $value = $this->value($key);

        if ($value !== null && !is_array($value)) {
            throw new InvalidArgumentException(sprintf('Invalid array patch value [%s].', $value));
        }

        return new ArrayPatchValue(
            present: $this->isPresent($key),
            value  : $value,
        );
    }

    /**
     * @template TKey of key-of<TInput>
     * @param TKey $key
     */
    public function isPresent(string $key): bool
    {
        return array_key_exists($key, $this->input);
    }

    /**
     * @template TKey of key-of<TInput>
     * @param TKey $key
     * @return TInput[TKey]|null
     */
    public function value(string $key): mixed
    {
        return $this->input[$key] ?? null;
    }
}
