<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator;

use Closure;
use Vaened\DeltaOrchestrator\Bindings\Behavior;
use Vaened\DeltaOrchestrator\Bindings\Optional;
use Vaened\DeltaOrchestrator\Bindings\Required;
use Vaened\DeltaOrchestrator\Comparison\Comparator;
use Vaened\DeltaOrchestrator\Comparison\StrictComparator;
use Vaened\DeltaOrchestrator\Patch\PatchValue;

/**
 * @template TValue
 * @template TResolved
 * @template TCurrent
 *
 * @implements Behavior<TValue, TResolved, TCurrent>
 */
final class Field implements Behavior
{
    /**
     * @var LazyValue<TCurrent>
     */
    private LazyValue $current;

    /**
     * @var LazyValue<bool>
     */
    private LazyValue $matches;

    /**
     * @var LazyValue<TResolved>
     */
    private LazyValue $value;

    /**
     * @var Closure(TValue): TResolved
     */
    private Closure $transformer;

    /**
     * @param Comparator|Closure(TResolved, TCurrent): bool|null $comparator
     */
    private Comparator|Closure|null $comparator;

    /**
     * @param PatchValue<TValue> $patch
     * @param TCurrent|Closure(): TCurrent $current
     * @param Comparator|Closure(TResolved, TCurrent): bool|null $comparator
     */
    public function __construct(
        private readonly PatchValue $patch,
        mixed                       $current,
        Comparator|Closure|null     $comparator = null,
    )
    {
        $this->comparator  = $comparator;
        $this->transformer = static fn(mixed $value): mixed => $value;

        $this->current = new LazyValue($this->resolveCurrent($current));
        $this->value   = new LazyValue($this->resolveValue());
        $this->matches = new LazyValue($this->resolve($this->comparator));
    }

    /**
     * @template TFromValue
     * @template TFromCurrent
     *
     * @param PatchValue<TFromValue> $patch
     * @param TFromCurrent|Closure(): TFromCurrent $current
     * @param Comparator|Closure(TFromValue, TFromCurrent): bool|null $comparator
     *
     * @return Field<TFromValue, TFromValue, TFromCurrent>
     */
    public static function from(
        PatchValue              $patch,
        mixed                   $current,
        Comparator|Closure|null $comparator = null,
    ): self
    {
        return new self(
            patch     : $patch,
            current   : $current,
            comparator: $comparator,
        );
    }

    /**
     * @template TIn
     * @template TOut
     * @param Closure(TIn): TOut $transformer
     * @return Closure(TIn|null): (TOut|null)
     */
    public static function notNullable(Closure $transformer): Closure
    {
        return static function (mixed $value) use ($transformer): mixed {
            if ($value === null) {
                return null;
            }

            return $transformer($value);
        };
    }

    /**
     * @return TResolved
     */
    public function value(): mixed
    {
        return $this->value->get();
    }

    /**
     * @return TCurrent
     */
    public function current(): mixed
    {
        return $this->current->get();
    }

    public function matches(): bool
    {
        return $this->matches->get();
    }

    public function satisfies(): bool
    {
        return $this->value() !== null;
    }

    public function changed(): bool
    {
        return !$this->matches();
    }

    /**
     * @return Delta<TCurrent, TResolved>|null
     */
    public function delta(): ?Delta
    {
        if (!$this->changed()) {
            return null;
        }

        return new Delta(
            previous: $this->current(),
            next    : $this->value(),
        );
    }

    /**
     * @return Field<TValue, TResolved, TCurrent>
     */
    public function field(): Field
    {
        return $this;
    }

    /**
     * @return Required<TValue, TResolved, TCurrent>
     */
    public function required(): Required
    {
        return new Required($this);
    }

    /**
     * @return Optional<TValue, TResolved, TCurrent>
     */
    public function optional(): Optional
    {
        return new Optional($this);
    }

    public function isPresent(): bool
    {
        return $this->patch()->isPresent();
    }

    public function isAbsent(): bool
    {
        return !$this->isPresent();
    }

    /**
     * @return TResolved|TCurrent
     */
    public function effective(): mixed
    {
        return $this->isPresent() ? $this->value() : $this->current();
    }

    /**
     * @param Comparator|Closure(TResolved, TCurrent): bool $comparator
     */
    public function using(Comparator|Closure $comparator): self
    {
        $this->comparator = $comparator;
        $this->matches    = new LazyValue($this->resolve($this->comparator));

        return $this;
    }

    /**
     * @template TNextResolved
     * @param Closure(TValue): TNextResolved $transformer
     * @return Field<TValue, TNextResolved, TCurrent>
     */
    public function transform(Closure $transformer): self
    {
        $this->transformer = $transformer;
        $this->value       = new LazyValue($this->resolveValue());
        $this->matches     = new LazyValue($this->resolve($this->comparator));

        return $this;
    }

    /**
     * @return PatchValue<TValue>
     */
    private function patch(): PatchValue
    {
        return $this->patch;
    }

    private function resolve(Comparator|Closure|null $comparator): callable
    {
        return function () use ($comparator) {
            if (!$this->isPresent()) {
                return true;
            }

            $value      = $this->value();
            $current    = $this->current();
            $comparator ??= StrictComparator::create();

            if ($comparator instanceof Closure) {
                return ($comparator)($value, $current);
            }

            return $comparator->equals($value, $current);
        };
    }

    /**
     * @return Closure(): TResolved
     */
    private function resolveValue(): Closure
    {
        return function () {
            $value = $this->patch()->value();

            if (!$this->isPresent()) {
                return $value;
            }

            return ($this->transformer)($value);
        };
    }

    /**
     * @param TCurrent|Closure(): TCurrent $current
     * @return Closure(): TCurrent
     */
    private function resolveCurrent(mixed $current): Closure
    {
        if ($current instanceof Closure) {
            return $current;
        }

        return static fn() => $current;
    }
}
