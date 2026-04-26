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
use Vaened\DeltaOrchestrator\Patch\PatchValue;

/**
 * @template TValue
 * @template TCurrent
 */
final class Field implements Behavior
{
    /**
     * @var LazyValue<PatchValue<TValue>>
     */
    private LazyValue $patch;

    /**
     * @var LazyValue<TCurrent>
     */
    private LazyValue $current;

    /**
     * @var LazyValue<bool>
     */
    private LazyValue $matches;

    /**
     * @param Closure(): PatchValue<TValue> $value
     * @param Closure(): TCurrent $current
     * @param Comparator|Closure(TValue, TCurrent): bool|null $comparator
     */
    public function __construct(
        Closure                 $value,
        Closure                 $current,
        Comparator|Closure|null $comparator = null,
    )
    {
        $this->patch   = new LazyValue($value);
        $this->current = new LazyValue($current);
        $this->matches = new LazyValue($this->resolve($comparator));
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

    public function field(): Field
    {
        return $this;
    }

    public function required(): Required
    {
        return new Required($this);
    }

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
        return $this->patch->get();
    }

    private function resolve(Comparator|Closure|null $comparator): callable
    {
        return function () use ($comparator) {
            if (!$this->isPresent()) {
                return true;
            }

            $value   = $this->value();
            $current = $this->current();

            if ($comparator instanceof Comparator) {
                return $comparator->equals($value, $current);
            }

            if ($comparator instanceof Closure) {
                return ($comparator)($value, $current);
            }

            return $value === $current;
        };
    }
}
