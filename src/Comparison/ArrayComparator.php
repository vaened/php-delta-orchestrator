<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Comparison;

use function array_key_exists;
use function count;
use function is_array;

final readonly class ArrayComparator implements Comparator
{
    private Comparator $itemComparator;

    public function __construct(?Comparator $itemComparator = null)
    {
        $this->itemComparator = $itemComparator ?? StrictComparator::create();
    }

    public static function create(?Comparator $itemComparator = null): self
    {
        return new self($itemComparator);
    }

    public function equals(mixed $value, mixed $current): bool
    {
        if (!$this->bothAreArrays($value, $current)) {
            return false;
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
}
