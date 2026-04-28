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
 * @template TCurrent
 *
 * @implements Behavior<TValue, TCurrent>
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
     * @param PatchValue<TValue> $patch
     * @param TCurrent|Closure(): TCurrent $current
     * @param Comparator|Closure(TValue, TCurrent): bool|null $comparator
     */
    public function __construct(
        private readonly PatchValue $patch,
        mixed                       $current,
        Comparator|Closure|null     $comparator = null,
    )
    {
        $this->current = new LazyValue($this->resolveCurrent($current));
        $this->matches = new LazyValue($this->resolve($comparator));
    }

    /**
     * @template TFromValue
     * @template TFromCurrent
     *
     * @param PatchValue<TFromValue> $patch
     * @param TFromCurrent|Closure(): TFromCurrent $current
     * @param Comparator|Closure(TFromValue, TFromCurrent): bool|null $comparator
     *
     * @return Field<TFromValue, TFromCurrent>
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
     * @return TValue
     */
    public function value(): mixed
    {
        return $this->patch()->value();
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
     * @return Delta<TCurrent, TValue>|null
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
     * @return Field<TValue, TCurrent>
     */
    public function field(): Field
    {
        return $this;
    }

    /**
     * @return Required<TValue, TCurrent>
     */
    public function required(): Required
    {
        return new Required($this);
    }

    /**
     * @return Optional<TValue, TCurrent>
     */
    public function optional(): Optional
    {
        return new Optional($this);
    }

    public function isPresent(): bool
    {
        return $this->patch()->isPresent();
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
