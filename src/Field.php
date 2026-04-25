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

/**
 * @template TValue
 * @template TCurrent
 */
final class Field implements Behavior
{
    /**
     * @var LazyValue<TValue>
     */
    private LazyValue $value;

    /**
     * @var LazyValue<TCurrent>
     */
    private LazyValue $current;

    /**
     * @var LazyValue<bool>
     */
    private LazyValue $matches;

    /**
     * @param Closure(): TValue $value
     * @param Closure(): TCurrent $current
     * @param Comparator|Closure(TValue, TCurrent): bool|null $comparator
     */
    public function __construct(
        Closure                 $value,
        Closure                 $current,
        Comparator|Closure|null $comparator = null,
    )
    {
        $this->value   = new LazyValue($value);
        $this->current = new LazyValue($current);
        $this->matches = new LazyValue($this->resolve($comparator));
    }

    /**
     * @return TValue
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

    private function resolve(Comparator|Closure|null $comparator): callable
    {
        return function () use ($comparator) {
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
