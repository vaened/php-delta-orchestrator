<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Patch;

/**
 * @template TInput of array<string, mixed>
 */
final readonly class PatchInput
{
    /**
     * @param TInput $input
     * @param list<string> $expectedKeys
     */
    public function __construct(
        private array $input,
        private array $expectedKeys,
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
    public function isPresent(string $key): bool
    {
        return in_array($key, $this->expectedKeys, true);
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
