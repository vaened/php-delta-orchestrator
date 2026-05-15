<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Comparison;

use Closure;
use Vaened\DeltaOrchestrator\Exceptions\ComparisonTypeMismatch;

use function array_key_exists;
use function count;
use function is_array;

final readonly class ArrayComparator implements Comparator
{
    use HandlesNullComparison;

    private Comparator $itemComparator;

    public function __construct(Comparator|Closure|null $itemComparator = null)
    {
        $this->itemComparator = match (true) {
            $itemComparator instanceof Comparator => $itemComparator,
            $itemComparator instanceof Closure    => self::toComparator($itemComparator),
            default                               => StrictComparator::create(),
        };
    }

    public static function create(Comparator|Closure|null $itemComparator = null): self
    {
        return new self($itemComparator);
    }

    public function equals(mixed $value, mixed $current): bool
    {
        $equals = $this->compareNulls($value, $current);

        if ($equals !== null) {
            return $equals;
        }

        if (!$this->bothAreArrays($value, $current)) {
            throw ComparisonTypeMismatch::forArray($value, $current);
        }

        return $this->equalsArray($value, $current);
    }

    private function equalsArray(array $left, array $right): bool
    {
        if (count($left) !== count($right)) {
            return false;
        }

        foreach ($left as $key => $leftValue) {
            if (!array_key_exists($key, $right)) {
                return false;
            }

            if (!$this->equalsItem($leftValue, $right[$key])) {
                return false;
            }
        }

        return true;
    }

    private function equalsItem(mixed $leftValue, mixed $rightValue): bool
    {
        if ($this->bothAreArrays($leftValue, $rightValue)) {
            return $this->equalsArray($leftValue, $rightValue);
        }

        return $this->itemComparator->equals($leftValue, $rightValue);
    }

    private function bothAreArrays(mixed $left, mixed $right): bool
    {
        return is_array($left) && is_array($right);
    }

    private static function toComparator(Closure $itemComparator): Comparator
    {
        return new class($itemComparator) implements Comparator {
            public function __construct(private readonly Closure $comparator)
            {
            }

            public function equals(mixed $value, mixed $current): bool
            {
                return ($this->comparator)($value, $current);
            }
        };
    }
}
